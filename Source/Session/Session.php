<?php

namespace Waddup\Session;

class Session
{
    public const FLASH_SUCCESS = 'success';
    public const FLASH_WARNING = 'warning';
    public const FLASH_DANGER = 'danger';
    public const FLASH_INFO = 'info';

    /**
     * Initializes the session
     * @return bool
     */
    public static function init(): bool
    {
        if (!isset($_SESSION)) session_start();
        return true;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function unset(string $key): void
    {
        if (self::exists($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function unsetFlash(string $key): void
    {
        if (self::exists($key)) {
            unset($_SESSION['_flash'][$key]);
        }
    }

    public static function get(string $key): mixed
    {
        if (self::exists($key)) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * Check if a key exists in array
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function setFlash(string $key, array $options): void
    {
        self::initFlash();
        $flash_data[$key] = json_encode($options);
        $_SESSION['_flash'] = $flash_data;
    }

    public static function getFlash(string $key): ?string
    {
        if (self::exists('_flash') && !empty($_SESSION['_flash'][$key])) {
            $flash = $_SESSION['_flash'][$key];
            self::unsetFlash($key);
            return $flash;
        }
        return null;
    }

    public static function setFormErrors(array $data): void
    {
        self::set('form_errors', $data);
    }

    public static function inErrorBag(string $key): bool
    {
        return isset(self::getFormErrors()[$key]);
    }

    public static function getFormErrors(): ?array
    {
        return self::get('form_errors') ?? null;
    }

    protected static function initFlash(): void
    {
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
    }
}