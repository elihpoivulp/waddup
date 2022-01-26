<?php

namespace Waddup\Models;

use PDO;
use Waddup\Core\Model;
use Waddup\Exceptions\DBError;
use Waddup\Session\Session;

class User extends Model
{
    /**
     * Error bag. Contains errors found in validate function
     * @var array
     */
    protected array $errors = [];

    protected array $fields = [
        'name', 'username', 'password', 'email'
    ];

    private string $confirm_password = '';

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $key = str_replace('-', '_', $key);
            if (in_array($key, $this->fields) || property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @throws DBError
     */
    public function save(): bool
    {
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO users (name, username, email, password) VALUES (:name, :username, :email, :password)';
            $password = password_hash($this->password, 1);

            $s = self::db()->prepare($sql);
            $s->bindValue(':name', $this->name);
            $s->bindValue(':username', $this->username);
            $s->bindValue(':email', $this->email);
            $s->bindValue(':password', $password);

            return $s->execute();
        }
        return false;
    }

    /**
     * Validate the current property values
     * @return void
     * @throws DBError
     */
    public function validate(): void
    {
        $errors = [];

        $name = $this->name;
        Session::set('_name', $name);
        if (has_length_greater_than($name, 70)) {
            $errors['name']['message'] = 'Name can\'t have more than 70 characters.';
        } else if (has_length_less_than($name, 4)) {
            $errors['name']['message'] = 'Name can\'t be less than 4 characters.';
        }

        $username = $this->username;
        Session::set('_username', $username);
        if (has_length_greater_than($username, 32)) {
            $errors['username']['message'] = 'Username can\'t have more than 32 characters.';
        } else if (has_length_less_than($username, 4)) {
            $errors['username']['message'] = 'Username can\'t be less than 4 characters.';
        } else if (self::usernameExists($username)) {
            $errors['username']['message'] = 'Username already exists.';
        }

        $email = $this->email;
        Session::set('_email', $email);
        if (!is_valid_email($email)) {
            $errors['email']['message'] = 'Email not valid';
        } else if (has_length_greater_than($email, 70)) {
            $errors['email']['message'] = 'Email can\'t have more than 70 characters.';
        } else if (has_length_less_than($email, 4)) {
            $errors['email']['message'] = 'Email can\'t be less than 4 characters.';
        } else if (self::emailExists($email)) {
            $errors['email']['message'] = 'Email already exists.';
        }

        $p = $this->password;
        if (has_length_greater_than($p, 33)) {
            $errors['password']['message'] = 'Password can\'t have more than 70 characters.';
        } else if (has_length_less_than($p, 4)) {
            $errors['password']['message'] = 'Password can\'t be less than 4 characters.';
        } else if (!is_exactly($p, $this->confirm_password)) {
            $errors['password']['message'] = 'Passwords do not match.';
        }

        foreach (array_keys($errors) as $key) {
            if (!str_contains($key, 'password')) {
                Session::set('form_values', [$key => $this->$key]);
                $errors[$key]['value'] = $this->$key;
            }
        }
        Session::setFormErrors($errors);
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $email
     * @return bool
     * @throws DBError
     */
    public static function emailExists(string $email): bool
    {
        return self::_exists('email', $email);
    }

    /**
     * @param string $username
     * @return bool
     * @throws DBError
     */
    public static function usernameExists(string $username): bool
    {
        return self::_exists('username', $username);
    }

    /**
     * @throws DBError
     */
    protected static function _exists(string $col, string $val): bool
    {
        $s = self::db()->prepare("select id from users where $col = :$col");
        $s->execute([":$col" => $val]);
        return $s->rowCount() > 0;
    }
}