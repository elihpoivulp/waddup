<?php

namespace Waddup\Core;

use Waddup\Exceptions\MethodNotFound;

abstract class Controller
{
    public function __construct(protected View $view, protected Request $request)
    {
    }

    /**
     * @throws MethodNotFound
     */
    public function __call(string $name, array $arguments)
    {
        $method = "{$name}Action";
        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                $this->$method();
                $this->after();
            }
        } else {
            $controller = get_class($this);
            throw new MethodNotFound("Method \"$method\" (in controller $controller) not found");
        }
    }

    /**
     * Action filter: before -
     * Executed before the method is called.
     */
    protected function before()
    {
    }

    /**
     * Action filter: after -
     * Executed after the method is called.
     */
    protected function after()
    {
    }
}