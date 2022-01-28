<?php

namespace Waddup\Models;

use PDO;
use Waddup\Exceptions\DBError;

class RememberedLogins extends \Waddup\Core\Model
{
    /**
     * @throws DBError
     */
    public static function findOne(string $token): bool|self
    {
        $sql = 'select * from remembered_logins where token = :token';
        $s = self::db()->prepare($sql);
        $s->setFetchMode(PDO::FETCH_CLASS, self::class);
        $s->execute([':token' => $token]);
        return $s->fetch();
    }

    public function hasExpired(): bool
    {
        return strtotime($this->expiry_date) < time();
    }

    /**
     * @throws DBError
     */
    public function delete(string $token = ''): bool
    {
        $token = $token ?? $this->token;
        $sql = 'delete from remembered_logins where token = :token';
        $s = self::db()->prepare($sql);
        return $s->execute([':token' => $token]);
    }
}