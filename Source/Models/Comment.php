<?php

namespace Waddup\Models;

use PDO;
use Waddup\Core\Model;
use Waddup\Exceptions\DBError;

class Comment extends Model
{
    protected array $fields = ['user_id', 'body', 'post_id'];

    /**
     * @throws DBError
     */
    public static function findPostComments(int $post_id): bool|array
    {
        $sql = 'select c.*, u.name as writer from comments c left join users u on u.id = c.user_id where post_id = :id';
        $s = self::db()->prepare($sql);
        $s->setFetchMode(PDO::FETCH_CLASS, self::class);
        $s->execute([':id' => $post_id]);
        return $s->fetchAll();
    }

    /**
     * @throws DBError
     */
    public function author(): bool|User
    {
        if (!is_null($this->user_id)) {
            return User::findOne($this->user_id);
        } else {
            return false;
        }
    }

    /**
     * @throws DBError
     */
    public function save(): bool
    {
        if ($this->validate()) {
            $sql = 'insert into comments (post_id, user_id, body) values (:pid, :uid, :body)';
            $s = self::db()->prepare($sql);
            $s->bindValue(':pid', $this->post_id);
            $s->bindValue(':uid', $this->user_id === 'null' ? null : $this->user_id);
            $s->bindValue(':body', $this->body);
            return $s->execute();
        }
        return false;
    }

    protected function validate(): bool
    {
        $errors = [];
        if (has_length_greater_than($this->body, 250)) {
            $errors['body']['message'] = 'Body exceeds 250 maximum characters.';
        }
        $this->errors = $errors;
        return empty($this->errors());
    }
}