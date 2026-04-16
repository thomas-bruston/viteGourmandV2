<?php
// Feature : commandes — création, suivi, modification, annulation

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Commande;
use Repository\CommandeRepository;
use Repository\MenuRepository;
use Repository\UtilisateurRepository;
use Service\MailService;
use Service\PriceService;

class CommandeController extends Controller
{
    private CommandeRepository    $commandeRepository;
    private MenuRepository        $menuRepository;
    private UtilisateurRepository $utilisateurRepository;
    private MailService           $mailService;
    private PriceService          $priceService;

    public function __construct()
    {
        $this->commandeRepository   = new CommandeRepository();
        $this->menuRepository       = new MenuRepository();
        $this->utilisateurRepository = new UtilisateurRepository();
        $this->mailService          = new MailService();
        $this->priceService         = new PriceService();
    }

    public function mesCommandes(): void
    {
        $userId    = Session::getUserId();
        $commandes = $this->commandeRepository->findByUtilisateurId($userId);

        $commandesEnrichies = [];
        foreach ($commandes as $commande) {
            $menu = $this->menuRepository->findById($commande->getMenuId());
            $commandesEnrichies[] = [
                'commande'   => $commande,
                'menu_titre' => $menu ? $menu->getTitre() : '—',
                'menu_image' => $menu ? $menu->getImage() : '',
            ];
        }

        $this->render('commande/index', [
            'commandes' => $commandesEnrichies,
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

   public function showForm(): void
{
    $menuId          = (int) ($this->get('menu_id') ?: $this->post('menu_id'));
    $nombrePersonnes = (int) ($this->get('nombre_personnes') ?: 0);
    $menu            = $menuId > 0 ? $this->menuRepository->findById($menuId) : null;

    // le contrôleur garantit que la vue reçoit toujours un menu valide
    if ($menu === null) {
        Session::setFlash('error', 'Veuillez sélectionner un menu pour commander.');
        $this->redirect('/menus');
    }

    $userId      = Session::getUserId();
    $utilisateur = $this->utilisateurRepository->findById($userId);

    if ($nombrePersonnes < $menu->getNombrePersonneMinimum()) {
        $nombrePersonnes = $menu->getNombrePersonneMinimum();
    }

    $prix = $this->priceService->calculerTotal(
        $menu,
        $nombrePersonnes,
        '33000'
    );

    $this->render('commande/create', [
        'csrf_token'      => Session::generateCsrfToken(),
        'menu'            => $menu,
        'utilisateur'     => $utilisateur,
        'nombrePersonnes' => $nombrePersonnes,
        'prixMenu'        => $prix['prix_menu'],
        'prixLivraison'   => $prix['prix_livraison'],
        'prixTotal'       => $prix['prix_total'],
        'error'           => Session::getFlash('error'),
    ]);
}

public function store(): void
{
    $this->verifyCsrf();

    // --- Récupération et validation des inputs ---
    $menuId          = (int) $this->post('menu_id');
    $nombrePersonnes = (int) $this->post('nombre_personnes');
    $datePrestation  = trim($this->post('date_prestation'));
    $heureLivraison  = trim($this->post('heure_livraison'));
    $adresse         = trim($this->post('adresse_livraison'));
    $codePostal      = trim($this->post('code_postal_livraison'));
    $ville           = trim($this->post('ville_livraison'));
    $distanceKm           = (float) $this->post('distance_km', 0);
    $prixLivraisonCalcule = (float) $this->post('prix_livraison_calcule', 5.00);

    if (!$menuId || !$nombrePersonnes || !$datePrestation
        || !$heureLivraison || !$adresse || !$codePostal || !$ville) {
        Session::setFlash('error', 'Tous les champs sont obligatoires.');
        $this->redirect('/commander');
    }

    $menu = $this->menuRepository->findById($menuId);
    if ($menu === null) {
        Session::setFlash('error', 'Menu introuvable.');
        $this->redirect('/commander');
    }

    if ($menu->getQuantiteRestante() < $nombrePersonnes) {
        Session::setFlash('error',
            'Désolé, il ne reste que ' . $menu->getQuantiteRestante() . ' place(s) disponible(s).'
        );
        $this->redirect('/commander?menu_id=' . $menuId);
    }

    // ✅ Déclaré AVANT le try pour être accessible dans les catch
    $pdo = \Core\Database::getInstance()->getConnection();

    try {
        $pdo->beginTransaction();

        // Calcul du prix
        $prix = $this->priceService->calculerTotal($menu, $nombrePersonnes, $codePostal, $distanceKm);
        if ($prixLivraisonCalcule > 5.00) {
            $prix['prix_livraison'] = $prixLivraisonCalcule;
            $prix['prix_total']     = round($prix['prix_menu'] + $prixLivraisonCalcule, 2);
        }

        // Création de la commande
        $commande = new Commande();
        $commande->setUtilisateurId(Session::getUserId());
        $commande->setMenuId($menuId);
        $commande->setNumeroCommande($this->commandeRepository->generateNumeroCommande());
        $commande->setDatePrestation($datePrestation);
        $commande->setHeureLivraison($heureLivraison);
        $commande->setAdresseLivraison($adresse);
        $commande->setCodePostalLivraison($codePostal);
        $commande->setVilleLivraison($ville);
        $commande->setNombrePersonnes($nombrePersonnes);
        $commande->setPrixMenu($prix['prix_menu']);
        $commande->setPrixLivraison($prix['prix_livraison']);
        $commande->setPrixTotal($prix['prix_total']);

        $commandeId = $this->commandeRepository->create($commande);
        $this->commandeRepository->updateStatut($commandeId, Commande::STATUT_EN_ATTENTE, 'Commande reçue');

        // Décrémentation atomique
        $this->menuRepository->decrementerQuantite($menuId, $nombrePersonnes);

        $pdo->commit();

        // Mail hors transaction (échec non critique)
        try {
            $utilisateur = $this->utilisateurRepository->findById(Session::getUserId());
            $this->mailService->sendConfirmationCommande(
                $utilisateur->getEmail(),
                $utilisateur->getPrenom(),
                $commande->getNumeroCommande(),
                $menu->getTitre(),
                $datePrestation,
                $prix['prix_total']
            );
        } catch (\Exception $e) {
            error_log('[CommandeController::store] Échec envoi mail : ' . $e->getMessage());
        }

        Session::setFlash('success',
            'Votre commande ' . $commande->getNumeroCommande() . ' a bien été enregistrée !'
        );
        $this->redirect('/mes-commandes');

    } catch (\RuntimeException $e) {
        // ✅ inTransaction() avant tout rollBack()
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        Session::setFlash('error',
            'Ce menu n\'est plus disponible en quantité suffisante. Veuillez vérifier votre commande.'
        );
        error_log('[CommandeController::store] ' . $e->getMessage());
        $this->redirect('/commander?menu_id=' . $menuId);

    } catch (\InvalidArgumentException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        Session::setFlash('error', $e->getMessage());
        $this->redirect('/commander?menu_id=' . $menuId);
    }
}

    public function annuler(): void
    {
        $this->verifyCsrf();

        $commandeId = (int) $this->post('commande_id');
        $commande   = $this->commandeRepository->findById($commandeId);

        if ($commande === null || $commande->getUtilisateurId() !== Session::getUserId()) {
            $this->redirect('/mes-commandes');
        }

        if (!$commande->estModifiable()) {
            Session::setFlash('error', 'Cette commande ne peut plus être annulée.');
            $this->redirect('/mes-commandes');
        }

        $this->commandeRepository->annuler($commandeId, 'Annulée par le client');

        Session::setFlash('success', 'Commande annulée.');
        $this->redirect('/mes-commandes');
    }

    public function modifier(): void
    {
        $this->verifyCsrf();

        $commandeId     = (int) $this->post('commande_id');
        $action         = $this->post('action');
        $datePrestation = trim($this->post('date_prestation'));
        $heureLivraison = trim($this->post('heure_livraison'));

        $commande = $this->commandeRepository->findById($commandeId);

        if ($commande === null || $commande->getUtilisateurId() !== Session::getUserId()) {
            $this->redirect('/mes-commandes');
        }

        if (!$commande->estModifiable()) {
            Session::setFlash('error', 'Cette commande ne peut plus être modifiée.');
            $this->redirect('/mes-commandes');
        }

        if ($action === 'annuler') {
            $this->commandeRepository->annuler($commandeId, 'Annulée par le client');
            Session::setFlash('success', 'Commande annulée.');
            $this->redirect('/mes-commandes');
        }

        $this->commandeRepository->modifier($commandeId, $datePrestation, $heureLivraison);
        Session::setFlash('success', 'Commande mise à jour.');
        $this->redirect('/mes-commandes');
    }
}