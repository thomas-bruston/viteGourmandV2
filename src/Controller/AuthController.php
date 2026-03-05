<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Service\AuthService;
use Service\MailService;

/* AuthController */

class AuthController extends Controller
{
    private AuthService $authService;
    private MailService $mailService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->mailService = new MailService();
    }

    // Connexion

    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'csrf_token' => Session::generateCsrfToken(),
            'error'      => Session::getFlash('error'),
            'success'    => Session::getFlash('success'),
        ]);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = trim($this->post('email'));
        $password = $this->post('password');

        try {
            $utilisateur = $this->authService->login($email, $password);

            if ($utilisateur === null) {
                Session::setFlash('error', 'Email ou mot de passe incorrect.');
                $this->redirect('/connexion');
            }

            Session::setFlash('success', 'Bienvenue ' . htmlspecialchars($utilisateur->getPrenom()) . ' !');

            // Redirection selon le rôle
            $role = Session::getUserRole();
            $redirect = Session::get('redirect_after_login');
            Session::remove('redirect_after_login');

            if ($redirect) {
                $this->redirect($redirect);
            }

            match($role) {
                'administrateur' => $this->redirect('/admin'),
                'employe'        => $this->redirect('/employe'),
                default          => $this->redirect('/'),
            };

        } catch (\RuntimeException $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/connexion');
        }
    }

    // Inscription

    public function showRegister(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
        }

        $this->render('auth/register', [
            'csrf_token' => Session::generateCsrfToken(),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function register(): void
{
    $this->verifyCsrf();

    try {
        $utilisateurId = $this->authService->register($_POST);

        $this->mailService->sendBienvenue(
            trim($this->post('email')),
            trim($this->post('prenom'))
        );

        Session::setFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        $this->redirect('/connexion');

    } catch (\InvalidArgumentException | \RuntimeException $e) {
        Session::setFlash('error', $e->getMessage());
        $this->redirect('/inscription');
    }
}

    // Déconnexion

    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/connexion');
    }

    // Mot de passe oublié

    public function showForgot(): void
    {
        $this->render('auth/forgot', [
            'csrf_token' => Session::generateCsrfToken(),
            'error'      => Session::getFlash('error'),
            'success'    => Session::getFlash('success'),
        ]);
    }

    public function forgot(): void
    {
        $this->verifyCsrf();

        $email = trim($this->post('email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Adresse email invalide.');
            $this->redirect('/mot-de-passe-oublie');
        }

        try {
            $token = $this->authService->generateResetToken($email);

            if ($token !== null) {
                // Récupérer le prénom pour personnaliser le mail
                $utilisateur = (new \Repository\UtilisateurRepository())->findByEmail($email);
                $this->mailService->sendResetPassword($email, $utilisateur->getPrenom(), $token);
            }

            // Mail base
            
            Session::setFlash('success', 'Si un compte existe avec cet email, un lien de réinitialisation vous a été envoyé.');
            $this->redirect('/mot-de-passe-oublie');

        } catch (\Exception $e) {
            Session::setFlash('error', 'Une erreur est survenue. Veuillez réessayer.');
            $this->redirect('/mot-de-passe-oublie');
        }
    }

    // Réinitialisation du mot de passe

    public function showReset(): void
    {
        $token = trim($this->get('token'));

        if (empty($token)) {
            $this->redirect('/connexion');
        }

        $this->render('auth/reset', [
            'csrf_token' => Session::generateCsrfToken(),
            'token'      => htmlspecialchars($token),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function reset(): void
    {
        $this->verifyCsrf();

        $token    = trim($this->post('token'));
        $password = $this->post('mot_de_passe');
        $confirm  = $this->post('mot_de_passe_confirm');

        try {
            $this->authService->resetPassword($token, $password, $confirm);
            Session::setFlash('success', 'Mot de passe réinitialisé avec succès !');
            $this->redirect('/connexion');

        } catch (\InvalidArgumentException | \RuntimeException $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/reinitialiser-mdp?token=' . urlencode($token));
        }
    }
}
