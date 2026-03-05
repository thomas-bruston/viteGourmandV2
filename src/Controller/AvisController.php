<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Avis;
use Repository\AvisRepository;
use Repository\CommandeRepository;

/* AvisController */

class AvisController extends Controller
{
    private AvisRepository    $avisRepository;
    private CommandeRepository $commandeRepository;

    public function __construct()
    {
        $this->avisRepository    = new AvisRepository();
        $this->commandeRepository = new CommandeRepository();
    }

    /*Formulaire avis */

    public function showForm(): void
{
    $commandeId = (int) $this->get('commande_id');
    $commande   = null;

    if ($commandeId > 0) {
        $c = $this->commandeRepository->findById($commandeId);
        if ($c !== null && $c->getUtilisateurId() === Session::getUserId()) {
            $commande = $c;
        }
    }

    $this->render('user/avis-form', [
        'csrf_token' => Session::generateCsrfToken(),
        'commande'   => $commande,
        'error'      => Session::getFlash('error'),
        'old'        => [],
        'errors'     => [],
    ]);
}

    /* Enregistrement avis */

    public function store(): void
    {
        $this->verifyCsrf();

        $commandeId  = (int) $this->post('commande_id');
        $note        = (int) $this->post('note');
        $commentaire = trim($this->post('description'));

        if ($commandeId > 0) {
            $commande = $this->commandeRepository->findById($commandeId);
            if ($commande === null || $commande->getUtilisateurId() !== Session::getUserId()) {
                $this->redirect('/mes-commandes');
            }
        }

        try {
            $avis = new Avis();
            $avis->setUtilisateurId(Session::getUserId());
            $avis->setCommandeId($commandeId);
            $avis->setNote($note);
            $avis->setCommentaire($commentaire);

            $this->avisRepository->create($avis);

            Session::setFlash('success', 'Merci pour votre avis ! Il sera publié après modération.');
            $this->redirect('/mes-commandes');

        } catch (\InvalidArgumentException $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/avis/nouveau?commande_id=' . $commandeId);
        }
    }

    /*Liste avis */
    
    public function adminIndex(): void
    {
        $avis = $this->avisRepository->findAll();

        $this->render('employee/avis/index', [
            'avis'    => $avis,
            'success' => Session::getFlash('success'),
        ]);
    }

    public function valider(): void
    {
        $this->verifyCsrf();
        $avisId = (int) $this->post('avis_id');
        $this->avisRepository->updateStatut($avisId, Avis::STATUT_VALIDE);
        Session::setFlash('success', 'Avis validé.');
        $this->redirect('/employe/avis');
    }

    public function refuser(): void
    {
        $this->verifyCsrf();
        $avisId = (int) $this->post('avis_id');
        $this->avisRepository->updateStatut($avisId, Avis::STATUT_REFUSE);
        Session::setFlash('success', 'Avis refusé.');
        $this->redirect('/employe/avis');
    }
}
