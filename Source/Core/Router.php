<?php

namespace Waddup\Core;

use Waddup\Exceptions\ControllerNotFound;
use Waddup\Exceptions\MethodNotFound;
use Waddup\Exceptions\PageNotFound;

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
        $path = defined(CONTROLLERS_PATH) ? CONTROLLERS_PATH : 'Waddup\\App\\Controllers\\';
        $this->controller_namespace = str_replace('/', '\\', $path);
    }

    /**
     * Creates the controller and executes the action method
     * @return void
     * @throws PageNotFound
     * @throws ControllerNotFound
     * @throws MethodNotFound
     */
    public function dispatch(View $view, Request $request)
    {
        $this->current_path = $this->removeSlashes($request->getPath());
        $result = $this->removeQueryString()->resolve();

        // If a matching route in the routing table is found:
        if ($result) {
            $controller = $this->toStudlyCaps($this->params['controller'] ?? $this->default_controller);
            $controller = $this->getNamespace() . $this->removeSlashes($controller);

            if (class_exists($controller)) {
                $action = $this->toCamelCase($this->params['action'] ?? $this->default_action);
                $this->params['request_uri'] = $this->current_path;

                // Instantiate the controller
                $object = new $controller($view, $request);

                // If action exists in the controller, execute:
                if (is_callable([$object, $action])) {
                    $object->$action();
                } else {
                    throw new MethodNotFound("Method \"$action\" (in controller $controller) not found");
                }
            } else {
                throw new ControllerNotFound("Controller class \"$controller\" not found");
            }
        } else {
            throw new PageNotFound('No route matched');
        }
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

    protected function resolve(): bool
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

    /**
     * Removes query string to the requested URL, so it would not be confused
     * as action
     * @return self
     */
    protected function removeQueryString(): self
    {
        if (!empty($this->current_path)) {
            $parts = explode('&', $this->current_path, 2);
            $this->current_path = !str_contains($parts[0], '=') ? $parts[0] : '';
        }
        return $this;
    }

    protected function cleanURL(string $url): string
    {
        $url = $this->removeSlashes($url);

        // if empty, it must be the homepage
        if (empty($url)) return '/^$/';

        return $this->compileURL($url);
    }

    protected function removeSlashes(string $string): string
    {
        return trim_slashes($string);
    }

    /**
     * ConvertStringToStudlyFormat
     * TODO: maybe separate this as a helper function
     * @param string $string
     * @return string
     */
    protected function toStudlyCaps(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }

    /**
     * convertStringToCamelCase
     * TODO: maybe separate this as a helper function
     * @param string $string
     * @return string
     */
    protected function toCamelCase(string $string): string
    {
        return lcfirst($this->toStudlyCaps($string));
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

    /**
     * Returns controller in a given namespace if provided
     * @return string
     */
    protected function getNamespace(): string
    {
        $namespace = $this->controller_namespace;
        if (key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}