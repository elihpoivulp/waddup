<?php
/**
 * Front controller
 */
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Source/bootstrap.php';

use Waddup\Core\Request;
use Waddup\Core\Router;
use Waddup\Core\View;

$router = new Router();
$router->addRoute('');

// general route
$router->addRoute('{controller}/{action}');

// login route
$router->addRoute('login', ['controller' => 'Login']);
$router->addRoute('register', ['controller' => 'Register']);

$router->dispatch(new View(), new Request());
