<?php

namespace Waddup\Config;

class Config
{
    public const MAX_LOGIN_AGE = 60 * 60 * 24; // 1 day

    /**
     * Gets the secret key for generating hashes or encryption
     * @return string
     */
    public static function SECRET_KEY(): string
    {
        return $_ENV['SECRET_KEY'];
    }

    /**
     * Show errors or not
     * @return bool
     */
    public static function DEBUG(): bool
    {
        return $_ENV['DEBUG'] ?? 1;
    }

    public static function DB(string $key): string
    {
        $data = [
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'pass' => $_ENV['DB_PASS'],
            'host' => $_ENV['DB_HOST'],
            'port' => 3306,
            'driver' => $_ENV['DB_DRIVER'],
            'dsn' => $_ENV['DB_DSN']
        ];
        return $data[$key];
    }

    public static function EMAIL(string $key): string
    {
        return $_ENV[$key];
    }
}