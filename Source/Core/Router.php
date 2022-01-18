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
    protected string $controller_namespace;

    public function __construct()
    {
        $path = defined(CONTROLLERS_PATH) ? CONTROLLERS_PATH : 'Source\\App\\Controllers';
        $this->controller_namespace = str_replace('/', '\\', $path);
    }

    /**
     * Add route to the routes table
     * @param string $route
     * @param array $params
     * @return void
     */
    public function addRoute(string $route, array $params = [])
    {
        $this->routes[$this->cleanURL($route)] = $params;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function resolve(): bool // turn into private
    {
        $url = $_SERVER['QUERY_STRING'] ?? $_SERVER['REQUEST_URI'];
        $has_match = false;
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $this->removeSlashes($url), $matches)) {
                foreach ($matches as $key => $value) {
                    // if the key is a parameter defined in the compiled url,
                    // add its value as addition parameter
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                $has_match = true;
                $this->params = $params;
            }
        }
        return $has_match;
    }

    protected function cleanURL(string $url): string
    {
        $url = $this->removeSlashes($url);

        // if empty, it must be the homepage
        if (empty($url)) return '/^$/';

        return $this->compileURL($url);
    }

    protected function removeSlashes(string $url): string
    {
        return trim($url, '/');
    }

    /**
     * Converts URL into regular expression.
     * @param string $url
     * @return string
     */
    protected function compileURL(string $url): string
    {
        // escape forward slashes
        $url = str_replace('/', '\\/', $url);

        // convert dynamic variables into named parameters
        $url = preg_replace('/{([a-zA-Z\d\-_]+)}/', '(?P<$1>[a-zA-Z\d\-_)]+)', $url);

        // convert any custom variable
        $url = preg_replace('/{([a-zA-Z\d\-_]+):([^}]+)}/', '(?P<$1>$2)', $url);

        return '/^' . $url . '$/i';
    }
}