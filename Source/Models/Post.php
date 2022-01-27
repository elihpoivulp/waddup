<?php

namespace Waddup\Models;

use Waddup\Core\Model;
use Waddup\Exceptions\DBError;

class Post extends Model
{
    /**
     * @throws DBError
     */
    public static function getAll(): bool|array
    {
        $s = self::db()->prepare('select * from posts');
        $s->execute();
        return $s->fetchAll();
    }
}