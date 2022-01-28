<?php

namespace Waddup\Models;

use PDO;
use Waddup\Core\Model;
use Waddup\Exceptions\DBError;

class Post extends Model
{
    protected array $fields = ['user_id', 'body', 'description', 'archive'];

    public int $id;
    public string $date_created;

    /**
     * @throws DBError
     */
    public static function getAll(): bool|array
    {
        $s = self::db()->prepare('select * from posts order by id desc');
        $s->execute();
        return $s->fetchAll();
    }

    public function validate(): bool
    {
        if (has_length_greater_than($this->description, 250)) {
            $this->errors['description']['message'] = 'Description too long.';
        }
        return empty($this->errors);
    }

    /**
     * @throws DBError
     */
    public function save(): bool
    {
        if ($this->validate()) {
            $sql = 'insert into posts (user_id, body, description, archive) value (:id, :body, :description, :archive)';
            $s = self::db()->prepare($sql);
            $s->bindValue(':id', (int) $this->user_id, PDO::PARAM_INT);
            $s->bindValue(':body', $this->body, PDO::PARAM_STR);
            $s->bindValue(':description', $this->description);
            $s->bindValue(':archive', $this->archive === 'on' ? 1 : 0);
            return $s->execute();
        }
        return false;
    }
}