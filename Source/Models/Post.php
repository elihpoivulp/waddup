<?php

namespace Waddup\Models;

use Waddup\Core\Model;

class Post extends Model
{
    public static function getAll(): bool|array
    {
        $s = self::db()->prepare('select * from posts');
        $s->execute();
        return $s->fetchAll();
    }
}