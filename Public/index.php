<?php
/**
 * Front controller
 */

use Source\Core\Router;
use Source\Core\View;

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Source/bootstrap.php';

// autoload classes
spl_autoload_register(function ($class) {
    $file = BASE_PATH . "/$class.php";
    $file = fix_dir_sep($file);
    if (file_exists($file) && is_readable($file)) {
        require $file;
    }
});

$router = new Router();
$router->addRoute('');
$router->addRoute('posts/new', ['controller' => 'Posts', 'action' => 'new']);
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
$router->dispatch(new View());
