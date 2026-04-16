<?php
// Feature : menus publics — liste, détail, filtres AJAX

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Repository\MenuRepository;
use Repository\PlatRepository;
use Entity\Menu;

/* MenuController */

class MenuController extends Controller
{
    private MenuRepository $menuRepository;
    private PlatRepository $platRepository;

    public function __construct()
    {
        $this->menuRepository = new MenuRepository();
        $this->platRepository = new PlatRepository();
    }

    /* Liste des menus + filtres */

    public function index(): void
    {
        $menus   = $this->menuRepository->findAll();
        $themes  = $this->menuRepository->findAllThemes();
        $regimes = $this->menuRepository->findAllRegimes();

        $this->render('menus/index', [
            'menus'   => $menus,
            'themes'  => $themes,
            'regimes' => $regimes,
        ]);
    }

    
     /* filtres*/
     
    public function filtres(): void
{
    if (!$this->isAjax()) {
        $this->redirect('/menus');
    }

    $filters = [
        'theme'    => trim($this->get('theme')),
        'regime'   => trim($this->get('regime')),
        'prix_max' => $this->get('prix_max'),
        'personnes' => $this->get('personnes'),
    ];

    $filters = array_filter($filters, fn($v) => $v !== '' && $v !== null);

    $menus = $this->menuRepository->findWithFilters($filters);

    // Retourne HTML 
    extract(['menus' => $menus]);
    require_once TEMPLATES_PATH . '/menus/cards.php';
    exit;
}

    /* Détail menu */

    public function detail(): void
{
    $menuId = (int) $this->get('id');

    if ($menuId <= 0) {
        $this->redirect('/menus');
    }

    $menu = $this->menuRepository->findById($menuId);

    if ($menu === null) {
        $this->redirect('/menus');
    }

    // Récupére plats + allergènes
    
    $plats = $menu->getPlats();
    $allergenes = [];
    foreach ($plats as $plat) {
        foreach ($plat->getAllergenes() as $allergene) {
            if (!in_array($allergene, $allergenes)) {
                $allergenes[] = $allergene;
            }
        }
    }

    $this->render('menus/show', [
        'menu'      => $menu,
        'plats'     => $plats,
        'allergenes' => $allergenes,
    ]);
}

    // 
    // Espace employé — CRUD
    // 

    public function adminIndex(): void
    {
        $menus = $this->menuRepository->findAll();

        $this->render('employee/menus/index', [
            'menus'   => $menus,
            'success' => Session::getFlash('success'),
            'error'   => Session::getFlash('error'),
        ]);
    }
    public function showCreate(): void
{
    $this->render('employee/menus/create', [
        'csrf_token'     => Session::generateCsrfToken(),
        'themes'         => $this->menuRepository->findAllThemes(),
        'regimes'        => $this->menuRepository->findAllRegimes(),
        'tousAllergenes' => $this->platRepository->findAllAllergenes(),
        'error'          => Session::getFlash('error'),
    ]);
}
public function create(): void
{
    $this->verifyCsrf();

    try {
        $imageService = new \Service\ImageService();

        // Upload image menu
        $imageMenu = $imageService->upload(
            $_FILES['menu_image'],
            'images/menus'
        );

        $menu = new Menu();
        $menu->setTitre($this->post('titre'));
        $menu->setDescription($this->post('description'));
        $menu->setNombrePersonneMinimum((int) $this->post('nombre_personne_minimum'));
        $menu->setPrixParPersonne((float) $this->post('prix_par_personne'));
        $menu->setQuantiteRestante(100);
        $menu->setImage($imageMenu);

        $menuId = $this->menuRepository->create($menu);

        $themeIds  = array_map('intval', (array) $this->post('theme_ids', []));
        $regimeIds = array_map('intval', (array) $this->post('regime_ids', []));
        $this->menuRepository->syncThemes($menuId, $themeIds);
        $this->menuRepository->syncRegimes($menuId, $regimeIds);

        // Créer les plats
        $platsData = $this->post('plats', []);
        foreach ($platsData as $i => $platData) {
            $nom         = trim($platData['nom'] ?? '');
            $categorieId = (int) ($platData['categorie_id'] ?? 0);
            $allergenes  = array_map('intval', (array) ($platData['allergenes'] ?? []));

            if (empty($nom) || $categorieId === 0) {
                continue;
            }

            // Upload image plat
            $imagePlat = '';
            if (!empty($_FILES['plats']['tmp_name'][$i]['image'])) {
                $filePlat = [
                    'tmp_name' => $_FILES['plats']['tmp_name'][$i]['image'],
                    'size'     => $_FILES['plats']['size'][$i]['image'],
                    'type'     => $_FILES['plats']['type'][$i]['image'],
                ];
                $imagePlat = $imageService->upload($filePlat, 'images/plates');
            }

            $plat = new \Entity\Plat();
            $plat->setMenuId($menuId);
            $plat->setCategorieId($categorieId);
            $plat->setNom($nom);
            $plat->setImage($imagePlat);

            $platId = $this->platRepository->create($plat);

            if (!empty($allergenes)) {
                $this->platRepository->syncAllergenes($platId, $allergenes);
            }
        }

        Session::setFlash('success', 'Menu créé avec succès !');
        $this->redirect('/employe/menus');

    } catch (\InvalidArgumentException $e) {
        Session::setFlash('error', $e->getMessage());
        $this->redirect('/employe/menu/nouveau');
    }
}

    public function showEdit(): void
{
    $menuId = (int) $this->get('id');
    $menu   = $this->menuRepository->findById($menuId);

    if ($menu === null) {
        $this->redirect('/employe/menus');
    }

    $this->render('employee/menus/show', [
        'csrf_token'     => Session::generateCsrfToken(),
        'menu'           => $menu,
        'plats'          => $menu->getPlats(),
        'themes'         => $this->menuRepository->findAllThemes(),
        'regimes'        => $this->menuRepository->findAllRegimes(),
        'tousAllergenes' => $this->platRepository->findAllAllergenes(),
        'themeIds'       => $this->menuRepository->getThemeIds($menuId),
        'regimeIds'      => $this->menuRepository->getRegimeIds($menuId),
        'allergeneIds'   => $this->platRepository->findAllergeneIdsByMenuId($menuId),
        'error'          => Session::getFlash('error'),
    ]);
}

public function update(): void
{
    $this->verifyCsrf();

    $menuId = (int) $this->post('menu_id');
    $menu   = $this->menuRepository->findById($menuId);

    if ($menu === null) {
        $this->redirect('/employe/menus');
    }

    try {
        $imageService = new \Service\ImageService();

        $menu->setTitre($this->post('titre'));
        $menu->setDescription($this->post('description'));
        $menu->setNombrePersonneMinimum((int) $this->post('nombre_personne_minimum'));
        $menu->setPrixParPersonne((float) $this->post('prix_par_personne'));

        // Upload nouvelle image menu si fournie
        if (!empty($_FILES['menu_image']['tmp_name'])) {
            $nouvelleImage = $imageService->upload($_FILES['menu_image'], 'images/menus');
            $menu->setImage($nouvelleImage);
        }

        $this->menuRepository->update($menu);

        $themeIds  = array_map('intval', (array) $this->post('theme_ids', []));
        $regimeIds = array_map('intval', (array) $this->post('regime_ids', []));
        $this->menuRepository->syncThemes($menuId, $themeIds);
        $this->menuRepository->syncRegimes($menuId, $regimeIds);

        // MAJ des plats
        $platsData = $this->post('plats', []);
        foreach ($platsData as $platId => $platData) {
            $nom = trim($platData['nom'] ?? '');
            if (!empty($nom)) {
                $this->platRepository->updateNom((int) $platId, $nom);
            }

            // Upload nouvelle image plat si fournie
            if (!empty($_FILES['plat_photos']['tmp_name'][$platId])) {
                $filePlat = [
                    'tmp_name' => $_FILES['plat_photos']['tmp_name'][$platId],
                    'size'     => $_FILES['plat_photos']['size'][$platId],
                    'type'     => $_FILES['plat_photos']['type'][$platId],
                ];
                $nouvelleImagePlat = $imageService->upload($filePlat, 'images/plates');
                $this->platRepository->updateImage((int) $platId, $nouvelleImagePlat);
            }
        }

        Session::setFlash('success', 'Menu mis à jour avec succès.');
        $this->redirect('/employe/menus');

    } catch (\InvalidArgumentException $e) {
        Session::setFlash('error', $e->getMessage());
        $this->redirect('/employe/menu/modifier?id=' . $menuId);
    }
}

    public function delete(): void
    {
        $this->verifyCsrf();

        $menuId = (int) $this->post('menu_id');
        $this->menuRepository->delete($menuId);

        Session::setFlash('success', 'Menu supprimé.');
        $this->redirect('/employe/menus');
    }
}
