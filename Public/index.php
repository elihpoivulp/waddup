<?php
/**
 * Front controller
 * PHP Version 8.1
 */
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Source/bootstrap.php';

use Waddup\Core\Request;
use Waddup\Core\Router;
use Waddup\Core\View;

// run db check before displaying pages so even static pages
// that does not use the database will also fail to load.
Waddup\Core\DB\DBConnectionHandler::testConnection();

$router = new Router();

$router->addRoute('');

// Sandbox for testing PHP
$router->addRoute('sandbox', ['controller' => 'Sandbox']);

// login and register routes
$router->addRoute('login', ['controller' => 'Login']);
$router->addRoute('logout', ['controller' => 'Profile', 'action' => 'logout', 'namespace' => 'Profile']);
$router->addRoute('register', ['controller' => 'Register']);
$router->addRoute('register/store', ['controller' => 'Register', 'action' => 'store']);

// profile
$router->addRoute('profile', ['controller' => 'Profile', 'namespace' => 'Profile']);
$router->addRoute('profile/{action}', ['controller' => 'Profile', 'namespace' => 'Profile']);

// general route
// only works if the controller is not inside a subfolder or namespace
$router->addRoute('{controller}/{action}');

$router->dispatch(new View(), new Request());
