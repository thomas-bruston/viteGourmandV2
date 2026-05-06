<?php

/* Point d'entrée */ 

declare(strict_types=1);

// Définir chemin racine projet

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// Charger autoloader Composer 

require_once ROOT_PATH . '/vendor/autoload.php';

// Charger variables env

require_once SRC_PATH . '/Core/Env.php';
\Core\Env::load(ROOT_PATH . '/.env');

// Démarrer session

require_once SRC_PATH . '/Core/Session.php';
\Core\Session::start();

// Lancer routeur

require_once SRC_PATH . '/Core/Router.php';
$router = new \Core\Router();
$router->dispatch();
