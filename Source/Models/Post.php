<?php

namespace Waddup\Models;

use PDO;
use Waddup\Core\Model;
use Waddup\Exceptions\DBError;

class Post extends Model
{
    protected array $fields = ['user_id', 'body', 'description', 'archive'];

    /**
     * @throws DBError
     */
    public static function getAll(): bool|array
    {
        $s = self::db()->prepare('select *, (select count(id) from comments where post_id = posts.id) as comments_count from posts order by id desc');
        $s->setFetchMode(PDO::FETCH_CLASS, self::class);
        $s->execute();
        return $s->fetchAll();
    }

    /**
     * @throws DBError
     */
    public function comments(): bool|array
    {
        return Comment::findPostComments($this->id);
    }

    /**
     * @throws DBError
     */
    public static function findOne(int $id): bool|self
    {
        $sql = 'select p.*, count(c.id) as comments_count from posts p join comments c on p.id = c.post_id where p.id = :id';
        $s = self::db()->prepare($sql);
        $s->setFetchMode(PDO::FETCH_CLASS, self::class);
        $s->bindValue(':id', $id);
        $s->execute();
        return $s->fetch();
    }

    /**
     * @throws DBError
     */
    public function author(): bool|User
    {
        return User::findOne($this->user_id);
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
            $s->bindValue(':archive', $this->archive);
            return $s->execute();
        }
        return false;
    }
}