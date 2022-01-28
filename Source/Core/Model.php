<?php

namespace Waddup\Core;

use PDO;
use Waddup\Core\DB\DBConnectionHandler;
use Waddup\Exceptions\DBError;

abstract class Model extends DBConnectionHandler
{
    /**
     * Accepted fields list
     * @var array
     */
    protected array $fields = [];

    /**
     * Errors container
     * @var array
     */
    protected array $errors = [];

    public function __construct(array $data = [])
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $key = str_replace('-', '_', $key);
                if (in_array($key, $this->fields) || property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Get errors
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @throws DBError
     */
    protected static function db(): ?PDO
   {
       return self::connect();
   }
}