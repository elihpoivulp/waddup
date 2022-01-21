<?php

namespace Waddup\Core\DB;

use Exception;
use PDO;
use PDOException;
use Waddup\Config\Config;
use Waddup\Exceptions\DBError;

class DB
{
    // handler
    private static ?PDO $db = null;

    /**
     * @throws DBError
     */
    public function db(): PDO
    {
        if (is_null(self::$db)) {
            try {
                $options = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                );
                self::$db = new PDO(Config::DB('dsn'), Config::DB('user'), Config::DB('pass'), $options);
            } catch (PDOException | Exception $e) {
                throw new DBError($e->getMessage());
            }
        }
        return self::$db;
    }
}