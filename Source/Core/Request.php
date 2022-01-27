<?php

namespace Waddup\Core;

use Waddup\Exceptions\CSRFException;
use Waddup\Utils\CSRFToken;

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
        if(isset($_GET['_url'])){
            $this->current_path = $this->filterInput(INPUT_GET, '_url');
        } else {
            if (isset($_SERVER['QUERY_STRING']) && str_contains($_SERVER['QUERY_STRING'], '_url')) {
                $this->current_path = $_SERVER['QUERY_STRING'];
            } else {
                $this->current_path = $_SERVER['REQUEST_URI'];
            }
        }
    }

    public function getPath(): string
    {
        return $this->current_path;
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    /**
     * @throws CSRFException
     */
    public function isPost(): bool
    {
        if (!isset($_POST['_csrf'])) {
            throw new CSRFException('Missing CSRF token.');
        }
        if (!CSRFToken::validate($_POST['_csrf'])) {
            throw new CSRFException('CSRF token does not match.');
        }
        return !$this->isGet();
    }

    /**
     * Returns filtered values
     * @return array
     */
    public function getBody(): array
    {
        $body = [];
        $data = $this->isGet() ? [$_GET, INPUT_GET] : [$_POST, INPUT_POST];
        $input_type = $data[1];
        for ($i = 0, $data_keys = array_keys($data[0]); $i < count($data_keys); $i++) {
            $key = $data_keys[$i];
            $body[$key] = self::filterInput($input_type, $key);
        }
        return $body;
    }

    protected static function getHost(): string
    {
        return sprintf('%s%s', self::getProtocol(), $_SERVER['HTTP_HOST']);
    }

    /**
     * Cleans a single POST/GET value
     * @param int $type
     * @param string $key
     * @param int $default_filter
     * @return string
     */
    protected function filterInput(int $type, string $key, int $default_filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS): string
    {
        return filter_input($type, $key, $default_filter);
    }

    public static function getBaseURL(): string
    {
        return sprintf(
            '%s%s',
            self::getHost(),
            substr($_SERVER['SCRIPT_NAME'], 0, (strpos($_SERVER['SCRIPT_NAME'], "/Public") + 1))
        );
    }

    public static function getProtocol(): string
    {
        return 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://';
    }
}