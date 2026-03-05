<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Commande;
use Repository\CommandeRepository;
use Repository\MenuRepository;
use Service\MailService;
use Service\MongoService;

/* EmployeController */

class EmployeController extends Controller
{
    private CommandeRepository $commandeRepository;
    private MailService        $mailService;
    private MongoService       $mongoService;
    private MenuRepository     $menuRepository;

    public function __construct()
    {
        $this->commandeRepository = new CommandeRepository();
        $this->mailService        = new MailService();
        $this->mongoService       = new MongoService();
        $this->menuRepository     = new MenuRepository();
    }

    public function dashboard(): void
    {
        $commandes = $this->commandeRepository->findAll(['statut' => Commande::STATUT_EN_ATTENTE]);

        $this->render('employee/dashboard', [
            'commandes' => $commandes,
            'success'   => Session::getFlash('success'),
        ]);
    }

    /* Liste commandes + filtres */

    public function commandes(): void
    {
        $filters = [
            'statut'         => $this->get('statut'),
            'utilisateur_id' => $this->get('utilisateur_id'),
        ];

        $filters = array_filter($filters, fn($v) => $v !== '');
        $commandes = $this->commandeRepository->findAll($filters);

        $this->render('employee/commandes/index', [
            'commandes'      => $commandes,
            'statuts_valides' => Commande::STATUTS_VALIDES,
            'filtre_statut'  => $this->get('statut'),
            'success'        => Session::getFlash('success'),
            'error'          => Session::getFlash('error'),
        ]);
    }

    /* MAJ status + mail */
     
    public function updateStatut(): void
    {
        $this->verifyCsrf();

        $commandeId = (int) $this->post('commande_id');
        $statut     = trim($this->post('statut'));
        $commentaire = trim($this->post('commentaire', ''));

        if (!in_array($statut, Commande::STATUTS_VALIDES)) {
            Session::setFlash('error', 'Statut invalide.');
            $this->redirect('/employe/commandes');
        }

        $commande = $this->commandeRepository->findById($commandeId);

        if ($commande === null) {
            $this->redirect('/employe/commandes');
        }

        $this->commandeRepository->updateStatut($commandeId, $statut, $commentaire ?: null);

        // Mails automatiques selon le statut

        $this->envoyerMailStatut($commande, $statut);

      // Enregistrer dans MongoDB 

        if ($statut === Commande::STATUT_TERMINEE) {
            $menu = $this->menuRepository->findById($commande->getMenuId());

            $this->mongoService->enregistrerCommande(
                $commandeId,
                $commande->getMenuId(),
                $menu ? $menu->getTitre() : 'Menu #' . $commande->getMenuId(),
                date('Y-m-d'),
                $commande->getNombrePersonnes(),
                $commande->getPrixTotal()
            );
        }

        Session::setFlash('success', 'Statut mis à jour.');
        $this->redirect('/employe/commandes');
    }

    /* Annulation commande employé */

    public function annuler(): void
    {
        $this->verifyCsrf();

        $commandeId  = (int) $this->post('commande_id');
        $motif       = trim($this->post('motif_annulation'));
        

        if (empty($motif)) {
            Session::setFlash('error', 'Le motif et le mode de contact sont obligatoires.');
            $this->redirect('/employe/commandes');
        }

        $this->commandeRepository->annuler($commandeId, $motif);

        Session::setFlash('success', 'Commande annulée.');
        $this->redirect('/employe/commandes');
    }

    /* Envoie mails */

    private function envoyerMailStatut(Commande $commande, string $nouveauStatut): void
    {
        // Pour récupérer email + prénom du client
        $utilisateur = (new \Repository\UtilisateurRepository())->findById($commande->getUtilisateurId());

        if ($utilisateur === null) return;

        match($nouveauStatut) {
            Commande::STATUT_EN_ATTENTE_RETOUR_MATERIEL => $this->mailService->sendRetourMateriel(
                $utilisateur->getEmail(),
                $utilisateur->getPrenom(),
                $commande->getNumeroCommande()
            ),
            Commande::STATUT_TERMINEE => $this->mailService->sendInvitationAvis(
                $utilisateur->getEmail(),
                $utilisateur->getPrenom(),
                $commande->getNumeroCommande(),
                $commande->getCommandeId()
            ),
            default => null,
        };
    }
}
