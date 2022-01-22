<?php

namespace Waddup\Config;

class Config
{
    /**
     * Show errors or not
     * @var bool
     */
    public static bool $DEBUG = false;

    public static function DB(string $key): string
    {
        $data = [
            'name' => 'waddup',
            'user' => 'root',
            'pass' => '',
            'host' => 'localhost',
            'port' => 3306,
            'driver' => 'mysql'
        ];
        $data['dsn'] = "{$data['driver']}:dbname={$data['name']};host={$data['host']};port={$data['port']};charset=utf8";

        return $data[$key];
    }
}