<?php

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

        $userId      = Session::getUserId();
        $utilisateur = $this->utilisateurRepository->findById($userId);

        $prixMenu      = 0;
        $prixLivraison = 5.00;
        $prixTotal     = 5.00;

        if ($menu) {
            if ($nombrePersonnes < $menu->getNombrePersonneMinimum()) {
                $nombrePersonnes = $menu->getNombrePersonneMinimum();
            }

            $prix = $this->priceService->calculerTotal(
                $menu,
                $nombrePersonnes,
                '33000'
            );
            $prixMenu      = $prix['prix_menu'];
            $prixLivraison = $prix['prix_livraison'];
            $prixTotal     = $prix['prix_total'];
        }

        $this->render('commande/create', [
            'csrf_token'      => Session::generateCsrfToken(),
            'menu'            => $menu,
            'utilisateur'     => $utilisateur,
            'nombrePersonnes' => $nombrePersonnes,
            'prixMenu'        => $prixMenu,
            'prixLivraison'   => $prixLivraison,
            'prixTotal'       => $prixTotal,
            'error'           => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        

        $menuId          = (int) $this->post('menu_id');
        $nombrePersonnes = (int) $this->post('nombre_personnes');
        $datePrestation  = trim($this->post('date_prestation'));
        $heureLivraison  = trim($this->post('heure_livraison'));
        $adresse         = trim($this->post('adresse_livraison'));
        $codePostal      = trim($this->post('code_postal_livraison'));
        $ville           = trim($this->post('ville_livraison'));
        $distanceKm           = (float) $this->post('distance_km', 0);
        $prixLivraisonCalcule = (float) $this->post('prix_livraison_calcule', 5.00); 
       

        if (!$menuId || !$nombrePersonnes || !$datePrestation || !$heureLivraison || !$adresse || !$codePostal || !$ville) {
            Session::setFlash('error', 'Tous les champs sont obligatoires.');
            $this->redirect('/commander');
        }

        $menu = $this->menuRepository->findById($menuId);
        if ($menu === null) {
            Session::setFlash('error', 'Menu introuvable.');
            $this->redirect('/commander');
        }

        if ($menu->getQuantiteRestante() < $nombrePersonnes) {
            Session::setFlash('error', 'Désolé, il ne reste que ' . $menu->getQuantiteRestante() . ' menus disponibles.');
            $this->redirect('/commander?menu_id=' . $menuId);
        }

        try {
            $prix = $this->priceService->calculerTotal($menu, $nombrePersonnes, $codePostal, $distanceKm);
            if ($prixLivraisonCalcule > 5.00) {
                $prix['prix_livraison'] = $prixLivraisonCalcule;
                $prix['prix_total']     = round($prix['prix_menu'] + $prixLivraisonCalcule, 2);
}
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
            $this->menuRepository->decrementerQuantite($menuId, $nombrePersonnes);

            $utilisateur = $this->utilisateurRepository->findById(Session::getUserId());
            $this->mailService->sendConfirmationCommande(
                $utilisateur->getEmail(),
                $utilisateur->getPrenom(),
                $commande->getNumeroCommande(),
                $menu->getTitre(),
                $datePrestation,
                $prix['prix_total']
            );

            Session::setFlash('success', 'Votre commande ' . $commande->getNumeroCommande() . ' a bien été enregistrée !');
            $this->redirect('/mes-commandes');

        } catch (\InvalidArgumentException $e) {
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