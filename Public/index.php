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
$router->test();