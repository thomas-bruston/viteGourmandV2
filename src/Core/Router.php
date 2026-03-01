<?php

declare(strict_types=1);

namespace Core;

/* Classe Router */

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        //Pages visiteur

        $this->add('GET',  '/',                'HomeController',    'index',          null);
        $this->add('GET',  '/menus',           'MenuController',    'index',          null);
        $this->add('GET',  '/menus/detail',    'MenuController',    'detail',         null);
        $this->add('GET',  '/menus/filtres',   'MenuController',    'filtres',        null); 
        $this->add('GET',  '/contact',         'ContactController', 'index',          null);
        $this->add('POST', '/contact',         'ContactController', 'store',          null);
        $this->add('GET',  '/cgv',             'PageController',    'cgv',            null);
        $this->add('GET',  '/mentions',        'PageController',    'mentions',       null);

        //Auth

        $this->add('GET',  '/inscription',     'AuthController',    'showRegister',   null);
        $this->add('POST', '/inscription',     'AuthController',    'register',       null);
        $this->add('GET',  '/connexion',       'AuthController',    'showLogin',      null);
        $this->add('POST', '/connexion',       'AuthController',    'login',          null);
        $this->add('GET',  '/deconnexion',     'AuthController',    'logout',         null);
        $this->add('GET',  '/mot-de-passe-oublie', 'AuthController', 'showForgot',   null);
        $this->add('POST', '/mot-de-passe-oublie', 'AuthController', 'forgot',       null);
        $this->add('GET',  '/reinitialiser-mdp',   'AuthController', 'showReset',    null);
        $this->add('POST', '/reinitialiser-mdp',   'AuthController', 'reset',        null);

        // User

        $this->add('GET',  '/mes-informations','UserController',    'showInfos',      'utilisateur');
        $this->add('POST', '/mes-informations','UserController',    'updateInfos',    'utilisateur');
        $this->add('GET',  '/mes-commandes',   'CommandeController','mesCommandes',   'utilisateur');
        $this->add('GET',  '/commander',       'CommandeController','showForm',       'utilisateur');
        $this->add('POST', '/commander',       'CommandeController','store',          'utilisateur');
        $this->add('POST', '/commande/annuler','CommandeController','annuler',        'utilisateur');
        $this->add('POST', '/commande/modifier','CommandeController','modifier',      'utilisateur');
        $this->add('GET',  '/avis/nouveau',    'AvisController',    'showForm',       'utilisateur');
        $this->add('POST', '/avis/nouveau',    'AvisController',    'store',          'utilisateur');

        // Employé

        $this->add('GET',  '/employe',                  'EmployeController', 'dashboard',      'employe');
        $this->add('GET',  '/employe/commandes',        'EmployeController', 'commandes',      'employe');
        $this->add('POST', '/employe/commande/statut',  'EmployeController', 'updateStatut',   'employe');
        $this->add('POST', '/employe/commande/annuler', 'EmployeController', 'annuler',        'employe');
        $this->add('GET',  '/employe/menus',            'MenuController',    'adminIndex',     'employe');
        $this->add('GET',  '/employe/menu/nouveau',     'MenuController',    'showCreate',     'employe');
        $this->add('POST', '/employe/menu/nouveau',     'MenuController',    'create',         'employe');
        $this->add('GET',  '/employe/menu/modifier',    'MenuController',    'showEdit',       'employe');
        $this->add('POST', '/employe/menu/modifier',    'MenuController',    'update',         'employe');
        $this->add('POST', '/employe/menu/supprimer',   'MenuController',    'delete',         'employe');
        $this->add('GET',  '/employe/avis',             'AvisController',    'adminIndex',     'employe');
        $this->add('POST', '/employe/avis/valider',     'AvisController',    'valider',        'employe');
        $this->add('POST', '/employe/avis/refuser',     'AvisController',    'refuser',        'employe');
        $this->add('POST', '/employe/horaires',         'HoraireController', 'update',         'employe');
        $this->add('GET',  '/employe/messages',         'ContactController', 'adminIndex',     'employe');
        $this->add('POST', '/employe/messages/supprimer','ContactController','delete',         'employe');

        // -Admin

        $this->add('GET',  '/admin',                    'AdminController',   'dashboard',      'administrateur');
        $this->add('GET',  '/admin/employes',           'AdminController',   'employes',       'administrateur');
        $this->add('GET',  '/admin/employe/nouveau',    'AdminController',   'showCreate',     'administrateur');
        $this->add('POST', '/admin/employe/nouveau',    'AdminController',   'createEmploye',  'administrateur');
        $this->add('POST', '/admin/employe/desactiver', 'AdminController',   'desactiver',     'administrateur');
        $this->add('GET',  '/admin/statistiques',       'AdminController',   'statistiques',   'administrateur');
        $this->add('GET',  '/admin/stats',              'AdminController',   'statistiques',   'administrateur');
    }

    /* Ajoute route */

    private function add(
        string  $method,
        string  $path,
        string  $controller,
        string  $action,
        ?string $role
    ): void {
        $this->routes[] = compact('method', 'path', 'controller', 'action', 'role');
    }

    /* Dispatch contrôleur */

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = rtrim($uri, '/') ?: '/';
    

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {

                // Vérif role

                $this->checkAccess($route['role']);

                // Instance

                $controllerClass = 'Controller\\' . $route['controller'];
                $action          = $route['action'];

                if (!class_exists($controllerClass)) {
                    $this->abort(500, "Contrôleur introuvable : {$controllerClass}");
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    $this->abort(500, "Action introuvable : {$action}");
                    return;
                }

                $controller->$action();
                return;
            }
        }

        // Erreur 404

        $this->abort(404);
    }

    /* Vérif acces avec role */

    private function checkAccess(?string $requiredRole): void
    {
        if ($requiredRole === null) {
            return; 
        }

        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            Session::set('redirect_after_login', $_SERVER['REQUEST_URI']);
            header('Location: /connexion');
            exit;
        }

        $userRole = Session::getUserRole();

        // Hiérarchie 

        $hierarchy = ['utilisateur' => 1, 'employe' => 2, 'administrateur' => 3];

        $userLevel     = $hierarchy[$userRole]     ?? 0;
        $requiredLevel = $hierarchy[$requiredRole] ?? 0;

        if ($userLevel < $requiredLevel) {
            $this->abort(403);
        }
    }

    /* Gestion erreurs */
    
    private function abort(int $code, string $message = ''): void
    {
        http_response_code($code);

        $messages = [
            403 => 'Accès refusé — vous n\'avez pas les droits nécessaires.',
            404 => 'Page introuvable.',
            500 => 'Erreur interne du serveur.',
        ];

        $display = $message ?: ($messages[$code] ?? 'Erreur inconnue.');

        // Erreur generoque

        include TEMPLATES_PATH . '/errors/' . $code . '.php'
            ?: require_once TEMPLATES_PATH . '/errors/generic.php';
        exit;
    }
}
