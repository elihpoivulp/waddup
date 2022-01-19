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
$router->addRoute('posts/new', ['controller' => 'Posts', 'action' => 'new']);
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
$router->dispatch(new View(), new Request());
