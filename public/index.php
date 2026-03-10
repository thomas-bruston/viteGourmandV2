<?php

/* Point d'entrée */ 

declare(strict_types=1);

// Définir le chemin racine du projet

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// Charger l'autoloader Composer 

require_once ROOT_PATH . '/vendor/autoload.php';

// Charger les variables d'environnement

require_once SRC_PATH . '/Core/Env.php';
\Core\Env::load(ROOT_PATH . '/.env');

// Démarrer la session

require_once SRC_PATH . '/Core/Session.php';
\Core\Session::start();

// Lancer le routeur

require_once SRC_PATH . '/Core/Router.php';
$router = new \Core\Router();
$router->dispatch();
