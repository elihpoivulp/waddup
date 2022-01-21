<?php

namespace Waddup\Core;

use PDO;
use Waddup\Core\DB\DBConnectionHandler;
use Waddup\Exceptions\DBError;

abstract class Model extends DBConnectionHandler
{
    /**
     * @throws DBError
     */
    protected static function db(): ?PDO
   {
       return self::connect();
   }
}