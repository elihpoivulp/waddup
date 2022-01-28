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
use Waddup\Models\LoggedInUser;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;
use Waddup\Utils\CSRFToken;

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
            $twig->addGlobal('app', $_ENV['APP_NAME'] ?? 'Waddup');
            $twig->addGlobal('user', LoggedInUser::getLoggedInUser(SessionUserAuth::getToken()));

            $url_func = function (string $uri = ''): string {
                return trim_slashes(Request::getBaseURL() . $uri);
            };

            $get_in_sess = function (string $key): mixed {
                return Session::get($key);
            };


            $twig->addFunction(new TwigFunction('is_logged_in', fn() => SessionUserAuth::isLoggedIn()));
            $twig->addFunction(new TwigFunction('csrf', fn() => CSRFToken::generate()));
            $twig->addFunction(new TwigFunction('load_asset', $url_func));
            $twig->addFunction(new TwigFunction('site_url', $url_func));
            $twig->addFunction(new TwigFunction('session', function (string $key) use ($get_in_sess): mixed {
                return $get_in_sess($key);
            }));
            $twig->addFunction(new TwigFunction('post_body', fn($c) => htmlspecialchars_decode(stripslashes($c))));
            $twig->addFunction(new TwigFunction('get_in_session_delete', function (string $key) use ($get_in_sess): mixed {
                $data = $get_in_sess($key);
                Session::unset($key);
                return $data;
            }));
            $twig->addFunction(new TwigFunction('error_bag', function (string $key): bool {
                return Session::inErrorBag($key);
            }));
            $twig->addFunction(new TwigFunction('form_values', function (string $key): string|int|null {
                $val = null;
                if (Session::exists('form_values')) {
                    $val = Session::get('form_values')[$key] ?? null;
                }
                return $val;
            }));
            $twig->addFunction(new TwigFunction('display_form_errors', function (): void {
                if (Session::getFormErrors()) {
                    echo '<div class="ui error message">';
                    echo '<ul>';
                    foreach (Session::getFormErrors() as $error) {
                        if (is_array($error)) {
                            echo "<li>{$error['message']}</li>";
                        } else {
                            echo "<li>$error</li>";
                        }
                    }
                    echo '</ul>';
                    echo '</div>';
                    Session::unsetFormErrors();
                }
            }));

            $twig->addFunction(new TwigFunction('load_placeholder', function (string $folder = 'images/placeholders/avatar/large', string $file = 'ade.jpg') use ($url_func): string {
                return $url_func("assets/$folder/$file");
            }));

            // get flash from session
            $twig->addFunction(new TwigFunction('get_flash', function (string $key): ?string {
                return Session::getFlash($key);
            }));

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
        // NOTE: bad practice
        if ($template_name === 'error.twig') {
            $namespace = '';
        }
        $template_name = $this->getTemplate($template_name, $namespace);
        try {
            // when a 404 or 500 code is received, the loader fails to find
            // the /error.twig template by looking for it in whichever namespace the current view is using.
            // so catch the error and directly pass the template instead
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
            throw new Exception($e->getMessage());
        }
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

    /**
     * Gets the registered template
     * @param string $template_name
     * @param string|null $namespace
     * @return string
     */
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

}