<?php

namespace Waddup\Models;

use PDO;
use Waddup\Core\Model;
use Waddup\Exceptions\DBError;

class LoggedInUser extends Model
{
    /**
     * @throws DBError
     */
    public static function getLoggedInUser(string $token): User|bool
    {
        $sql = 'select u.* from active_logins al join users u on u.id = al.user_id where token = :token and date_logged_out is null';
        $s = self::db()->prepare($sql);
        $s->setFetchMode(PDO::FETCH_CLASS, User::class);
        $s->execute([':token' => $token]);
        return $s->fetch();
    }

    /**
     * @throws DBError
     */
    public static function logsOut(string $token): bool
    {
        $sql = 'update active_logins set date_logged_out = now() where token = :token';
        $s = self::db()->prepare($sql);
        return $s->execute([':token' => $token]);
    }
}