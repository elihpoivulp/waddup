<?php
/**
 * Front controller
 */
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Source/bootstrap.php';

// autoload classes
spl_autoload_register(function ($class) {
    $file = CONTROLLERS_PATH . "/$class.php";
    if (file_exists($file) && is_readable($file)) {
        require $file;
    }
});

$router = new Router();
$router->addRoute('');
$router->addRoute('posts/new', ['controller' => 'Posts', 'action' => 'new']);
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
echo '<pre>';
print_r($router->getRoutes());
if ($router->resolve()) {
    print_r($router->getParams());
}
echo '</pre>';