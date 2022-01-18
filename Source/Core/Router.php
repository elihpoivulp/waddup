<?php

class Router
{
    /**
     * Routes table: contains all the registered routes
     * @var array
     */
    protected array $routes;

    /**
     * Parameters associated to the requested route
     * @var array
     */
    protected array $params;

    /**
     * The current requested URL address
     * @var string
     */
    protected string $current_path;

    // defaults
    protected string $default_controller = 'Home';
    protected string $default_action = 'index';
    protected string $controller_namespace = 'Source\\App\\Controllers';

    public function test()
    {
        echo 'test from router';
    }
}