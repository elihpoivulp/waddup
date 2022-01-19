<?php

namespace Waddup\Core;

use Exception;
//use Waddup\Exceptions\ViewFileNotFound;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class View
{
    /**
     * Namespace for when registering template directory
     * @var string
     */
    protected static string $template_namespace = '';

    // Twig
    static protected ?Environment $twig = null;
    static protected ?FilesystemLoader $loader = null;

    public function __construct()
    {
        if (is_null(self::$twig)) {
            self::$loader = new FilesystemLoader(VIEWS_PATH);
            $twig = new Environment(self::$loader, [
                'debug' => true,
                'strict_variables' => true
            ]);
            $twig->addExtension(new DebugExtension());
            $twig->addGlobal('app', 'Waddup'); // TODO: pull from .env

            $url_func = function (string $uri = '') {
                return trim_slashes(Request::getSiteURL() . $uri);
            };

            $twig->addFunction(new TwigFunction('load_asset', $url_func));
            $twig->addFunction(new TwigFunction('site_url', $url_func));

            self::$twig = $twig;
        }
    }

    /**
     * Render a twig template
     * @param string $template_name
     * @param array $context
     * @param string|null $namespace
     * @return void
     * @throws Exception
     */
    public function render(string $template_name, array $context = [], ?string $namespace = null)
    {
        $template_name = $this->getTemplate($template_name, $namespace);
        try {
            echo self::$twig->render($template_name, $context);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Sets the template namespace for the given view template file
     * @param string $name
     * @return void
     */
    public function setTemplateNamespace(string $name)
    {
        self::$template_namespace = $name;
    }

    /**
     * Register a template path
     * @throws Exception
     */
    public function registerTemplatePath(string $directory = '', string $namespace = ''): void
    {
        try {
            $this->addPath($directory, $namespace);
        } catch (Exception $e) {
            throw new Exception('Cannot register template path.');
        }
    }


    private function getTemplate(string $template_name, ?string $namespace = null): string
    {
        $namespace = $namespace ?? static::$template_namespace;
        if (!empty($namespace)) {
            if (str_starts_with($template_name, '@')) {
                // $template_name = preg_replace('/^@\w+\/([\w\d\/.]$)/', "@$namespace/$1", $template_name);
                $parts = explode('/', $template_name);
                $template_name = "@$namespace/{$parts[1]}";
            } else {
                $template_name = sprintf('@%s/%s', $namespace, $template_name);
            }
        }
        return $template_name;
    }

    /**
     * @param string $directory
     * @param string $namespace
     * @return void
     * @throws Exception
     */
    private function addPath(string $directory, string $namespace = ''): void
    {
        try {
            self::$loader->addPath($directory, $namespace);
        } catch (LoaderError $e) {
            throw new Exception($e->getMessage());
        }
    }
}