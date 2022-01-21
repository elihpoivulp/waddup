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

$router = new Router();
$router->addRoute('');

// login and register routes
$router->addRoute('login', ['controller' => 'Login']);
$router->addRoute('register', ['controller' => 'Register']);

// profile
$router->addRoute('profile', ['controller' => 'Profile', 'namespace' => 'Profile']);

// general route
$router->addRoute('{controller}/{action}');

$router->dispatch(new View(), new Request());
