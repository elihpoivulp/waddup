<?php

namespace Waddup\Core;

/**
 * Handles data from the request
 */
class Request
{
    /**
     * The current requested URL address
     * @var string
     */
    protected string $current_path = '';

    public function __construct()
    {
        // $_SERVER['QUERY_STRING'] and $_GET['_url'] are ignored when the server
        // is served using PHP's built-in web server.
        if (isset($_GET['_url'])) {
            $this->current_path = self::filterInput(INPUT_GET, '_url');
        } else {
            $this->current_path = $_SERVER['QUERY_STRING'] ?? $_SERVER['REQUEST_URI'];
        }
    }

    public function getPath(): string
    {
        return $this->current_path;
    }

    protected static function appRoot(): string
    {
        return  sprintf(
            '%s%s',
            self::getBaseURL(),
            substr($_SERVER['SCRIPT_NAME'], 0, (strpos($_SERVER['SCRIPT_NAME'], "/Public") + 1))
        );
    }

    /**
     * Cleans a single POST/GET value
     * @param int $type
     * @param string $key
     * @param int $default_filter
     * @return string
     */
    public static function filterInput(int $type, string $key, int $default_filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS): string
    {
        return filter_input($type, $key, $default_filter);
    }

    public static function getBaseURL(): string
    {
        return sprintf('%s%s', self::getProtocol(), $_SERVER['HTTP_HOST']);
    }

    public static function getProtocol(): string
    {
        return 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://';
    }

    public static function getSiteURL(): string
    {
        return self::appRoot();
    }
}