<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Repository\UtilisateurRepository;


/* UserController */

class UserController extends Controller
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct()
    {
        $this->utilisateurRepository = new UtilisateurRepository();
    }

    public function showInfos(): void
{
    $utilisateur = $this->utilisateurRepository->findById(Session::getUserId());

    $this->render('user/profile', [
        'csrf_token' => Session::generateCsrfToken(),
        'user'       => [
            'nom'            => $utilisateur->getNom(),
            'prenom'         => $utilisateur->getPrenom(),
            'email'          => $utilisateur->getEmail(),
            'telephone'      => $utilisateur->getTelephone() ?? '',
            'adresse'        => $utilisateur->getAdresse() ?? '',
            'adresse_postale'=> $utilisateur->getCodePostal() ?? '',
            'ville'          => $utilisateur->getVille() ?? '',
        ],
        'errors'     => [],
        'success'    => Session::getFlash('success'),
        'error'      => Session::getFlash('error'),
    ]);
}

    public function updateInfos(): void
    {
        $this->verifyCsrf();

        $utilisateur = $this->utilisateurRepository->findById(Session::getUserId());

        try {
            $utilisateur->setNom($this->post('nom'));
            $utilisateur->setPrenom($this->post('prenom'));
            $utilisateur->setTelephone($this->post('telephone'));
            $utilisateur->setAdresse($this->post('adresse'));
            $utilisateur->setCodePostal($this->post('code_postal'));
            $utilisateur->setVille($this->post('ville'));

            $this->utilisateurRepository->update($utilisateur);

            // MAJ session
            
            Session::set('user_nom',    $utilisateur->getNom());
            Session::set('user_prenom', $utilisateur->getPrenom());

            Session::setFlash('success', 'Informations mises à jour.');
            $this->redirect('/mes-informations');

        } catch (\InvalidArgumentException $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/mes-informations');
        }
    }
}
