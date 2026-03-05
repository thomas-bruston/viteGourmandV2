<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Utilisateur;
use Repository\UtilisateurRepository;
use Service\AuthService;
use Service\MailService;
use Service\MongoService;

/* AdminController */

class AdminController extends Controller
{
    private UtilisateurRepository $utilisateurRepository;
    private MailService           $mailService;
    private MongoService          $mongoService;
    private AuthService           $authService;
    

    public function __construct()
    {
        $this->utilisateurRepository = new UtilisateurRepository();
        $this->mailService           = new MailService();
        $this->mongoService          = new MongoService();
        $this->authService           = new AuthService();
    }

    public function dashboard(): void
    {
        $employes = $this->utilisateurRepository->findAllEmployes();

        $this->render('admin/dashboard', [
            'employes' => $employes,
            'success'  => Session::getFlash('success'),
        ]);
    }

    public function employes(): void
    {
        $employes = $this->utilisateurRepository->findAllEmployes();

        $this->render('admin/employes/index', [
            'employes' => $employes,
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function showCreate(): void
    {
        $this->render('admin/employes/create', [
            'csrf_token' => Session::generateCsrfToken(),
            'error'      => Session::getFlash('error'),
        ]);
    }

    /* Création compte employé */

    public function createEmploye(): void
    {
        $this->verifyCsrf();

        $nom        = trim($this->post('nom'));
        $prenom     = trim($this->post('prenom'));
        $email      = trim($this->post('email'));
        $telephone  = trim($this->post('telephone'));
        $adresse    = trim($this->post('adresse'));
        $password   = $this->post('password');
        $confirm    = $this->post('confirm-password');

        try {
            if ($this->utilisateurRepository->emailExists($email)) {
                throw new \RuntimeException('Cet email est déjà utilisé.');
            }

            // Valid MDP

            $this->authService->validatePassword($password);

            if ($password !== $confirm) {
                throw new \InvalidArgumentException('Les mots de passe ne correspondent pas.');
            }

            $employe = new Utilisateur();
            $employe->setRoleId(2); 
            $employe->setNom($nom);
            $employe->setPrenom($prenom);
            $employe->setEmail($email);
            $employe->setMotDePasse(password_hash($password, PASSWORD_DEFAULT));
            $employe->setTelephone($telephone);
            $employe->setAdresse($adresse);
            

            $this->utilisateurRepository->create($employe);

            // Mail notif emploté
    
            $this->mailService->sendCreationCompteEmploye($email, $prenom);

            Session::setFlash('success', "Compte employé créé pour {$prenom} {$nom}. Pensez à lui communiquer son mot de passe.");
            $this->redirect('/admin/employes');

        } catch (\InvalidArgumentException | \RuntimeException $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/admin/employe/nouveau');
        }
    }

    /**
     * Désactive un compte employé
     */
    public function desactiver(): void
    {
        $this->verifyCsrf();
        $employeId = (int) $this->post('employe_id');
        $this->utilisateurRepository->setStatut($employeId, false);
        Session::setFlash('success', 'Compte employé désactivé.');
        $this->redirect('/admin/employes');
    }

    /* stats Mongo DB */
     
    public function statistiques(): void
{
    $dateDebut = $this->get('date_debut', date('Y-01-01'));
    $dateFin   = $this->get('date_fin',   date('Y-m-d'));

    $commandesParMenu = $this->mongoService->getNombreCommandesParMenu();
    $caParMenu        = $this->mongoService->getCAParMenu($dateDebut, $dateFin);

    // commanbdes + CA
    
    $statsParMenu = [];
    foreach ($commandesParMenu as $cmd) {
        $menuId = $cmd['menu_id'];
        $ca     = 0;
        foreach ($caParMenu as $c) {
            if ($c['menu_id'] === $menuId) {
                $ca = $c['chiffre_affaires'];
                break;
            }
        }
        $statsParMenu[] = [
            'menu_titre'    => $cmd['menu_titre'],
            'nb_commandes'  => $cmd['nombre_commandes'],
            'ca_total'      => $ca,
            'moy_personnes' => $cmd['nombre_commandes'] > 0
                ? round($cmd['total_personnes'] / $cmd['nombre_commandes'], 1)
                : 0,
        ];
    }

    $this->render('admin/stats', [
        'statsParMenu' => $statsParMenu,
        'ca_total'     => $this->mongoService->getCATotalPeriode($dateDebut, $dateFin),
        'dateDebut'    => $dateDebut,
        'dateFin'      => $dateFin,
    ]);
}}