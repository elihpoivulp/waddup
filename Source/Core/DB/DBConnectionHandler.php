<?php

namespace Waddup\Core\DB;

use Exception;
use PDO;
use PDOException;
use Waddup\Config\Config;
use Waddup\Exceptions\DBError;

class DBConnectionHandler
{
    protected static ?PDO $handler = null;

    /**
     * @throws DBError
     */
    protected static function connect(): PDO
    {
        if (is_null(self::$handler)) {
            try {
                $options = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                );
                self::$handler = new PDO(Config::DB('dsn'), Config::DB('user'), Config::DB('pass'), $options);
            } catch (PDOException|Exception $e) {
                throw new DBError($e->getMessage());
            }
        }
        return self::$handler;
    }

    /**
     * Test if there is connection to the database
     * @throws DBError
     */
    public static function testConnection(): void
    {
        try {
            self::connect();
        } catch (PDOException|Exception|DBError $e) {
            throw new DBError($e->getMessage());
        }
    }
}