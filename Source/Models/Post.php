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
        $sql = 'select p.*, u.username as writer, (select count(id) from comments where post_id = p.id) as comments_count from posts p join users u on u.id = p.user_id where p.expired = 0 order by id desc';
        return self::getResult($sql);
    }

    /**
     * @throws DBError
     */
    public static function getPostsForScroll(?int $id = null): bool|array
    {
        if ($id === 1) {
            return [];
        }
        $sql = 'select p.*, u.username as writer, ifnull((select count(id) from comments where post_id = p.id), 0) as comments_count from posts p join users u on u.id = p.user_id where p.id < :id and p.expired = 0 order by p.id desc limit 3';
        $params = [':id' => $id];
        if (is_null($id)) {
            $sql = 'select p.*, u.username as writer, ifnull((select count(id) from comments where post_id = p.id), 0) as comments_count from posts p join users u on u.id = p.user_id where p.expired = 0 order by p.id desc limit 3';
            $params = [];
        }
        return self::getResult($sql, $params);
    }

    /**
     * @throws DBError
     */
    public static function getArchivedPosts(int $id): array
    {
        $sql = 'select p.*, u.username as writer, (select count(id) from comments where post_id = p.id) as comments_count from posts p join users u on u.id = p.user_id where p.expired = 1 and p.user_id = :id order by id desc';
        $s = self::db()->prepare($sql);
        $s->execute([':id' => $id]);
        return $s->fetchAll();
    }

    /**
     * @throws DBError
     */
    public static function getActivePosts(int $id): array
    {
        $sql = 'select p.*, u.username as writer, (select count(id) from comments where post_id = p.id) as comments_count from posts p join users u on u.id = p.user_id where p.expired = 0 and p.user_id = :id order by id desc';
        $s = self::db()->prepare($sql);
        $s->execute([':id' => $id]);
        return $s->fetchAll();
    }

    /**
     * @throws DBError
     */
    private static function getResult(string $sql, array $params = []): bool|array
    {
        $s = self::db()->prepare($sql);
        $s->setFetchMode(PDO::FETCH_CLASS, self::class);
        if ($params) {
            $s->execute($params);
        } else {
            $s->execute();
        }
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
        $sql = 'select p.*, count(c.id) as comments_count, u.username as writer from posts p join comments c on p.id = c.post_id join users u on u.id = p.user_id where p.id = :id and expired = 0';
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