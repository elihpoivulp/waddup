<?php

namespace Waddup\Models;

use Exception;
use PDO;
use Waddup\Core\Model;
use Waddup\Exceptions\DBError;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;
use Waddup\Utils\Token;

class User extends Model
{
    /**
     * Error bag. Contains errors found in validate function
     * @var array
     */

    protected array $fields = [
        'name', 'username', 'password', 'email'
    ];

    protected string $confirm_password = '';

    /**
     * The expiry time for remember the login
     * @var int|float
     */
    public int|float $exp_time;

    /**
     * The token for authenticating the remember cookie
     * @var string
     */
    public string $token;

    /**
     * @throws DBError
     */
    public function save(): bool|int
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
            if ($s->execute()) {
                self::login(self::db()->lastInsertId());
                return true;
            }
        }
        return false;
    }

    /**
     * @throws DBError
     */
    public function update(string $type = 'profile'): bool
    {
        if ($type === 'profile') {
            $this->validate(true);
        } else {
            $this->validate();
        }

       if (empty($this->errors())) {
           $sql = 'update users set name = :name, email = :email, username = :username, password = :password where id = :id';
           $password = password_hash($this->password, 1);
           $s = self::db()->prepare($sql);
           $s->bindValue(':name', $this->name);
           $s->bindValue(':email', $this->email);
           $s->bindValue(':username', $this->username);
           $s->bindValue(':password', $password);
           $s->bindValue(':id', $this->id);
           return $s->execute();
       }
       return false;
    }

    public function editProp(string $prop, $val)
    {
        // if $prop value is same as previous, skip
        if (property_exists($this, $prop) && $val !== $this->$prop) {
            $this->$prop = $val;
        }
    }

    /**
     * Validate the current property values
     * @return void
     * @throws DBError
     */
    public function validate(bool $skip_passwords = false): void
    {
        $errors = [];

        $name = $this->name;
        if (has_length_greater_than($name, 70)) {
            $errors['name']['message'] = 'Name can\'t have more than 70 characters.';
        } else if (has_length_less_than($name, 4)) {
            $errors['name']['message'] = 'Name can\'t be less than 4 characters.';
        }

        $username = $this->username;
        if (has_length_greater_than($username, 32)) {
            $errors['username']['message'] = 'Username can\'t have more than 32 characters.';
        } else if (has_length_less_than($username, 4)) {
            $errors['username']['message'] = 'Username can\'t be less than 4 characters.';
        } else if (self::usernameExists($username, $this->id ?? null)) {
            $errors['username']['message'] = 'Username already exists.';
        }

        $email = $this->email;
        if (!is_valid_email($email)) {
            $errors['email']['message'] = 'Email not valid';
        } else if (has_length_greater_than($email, 70)) {
            $errors['email']['message'] = 'Email can\'t have more than 70 characters.';
        } else if (has_length_less_than($email, 4)) {
            $errors['email']['message'] = 'Email can\'t be less than 4 characters.';
        } else if (self::emailExists($email, $this->id ?? null)) {
            $errors['email']['message'] = 'Email already exists.';
        }

        if (!$skip_passwords) {
            $p = $this->password;
            if (has_length_greater_than($p, 33)) {
                $errors['password']['message'] = 'Password can\'t have more than 70 characters.';
            } else if (has_length_less_than($p, 4)) {
                $errors['password']['message'] = 'Password can\'t be less than 4 characters.';
            }
            if ($this->confirm_password) {
                if (!is_exactly($p, $this->confirm_password)) {
                    $errors['password']['message'] = 'Passwords do not match.';
                }
            }
        }

        if ($errors) {
            Session::set('form_values', [
                'name' => $name,
                'username' => $username,
                'email' => $email,
            ]);

        }

        Session::setFormErrors($errors);
        $this->errors = $errors;
    }

    /**
     * @throws DBError
     */
    public static function findOne(int $id): bool|self
    {
        $sql = 'select * from users where id = :id';
        $s = self::db()->prepare($sql);
        $s->setFetchMode(PDO::FETCH_CLASS, User::class);
        $s->execute([':id' => $id]);
        return $s->fetch();
    }

    /**
     * @throws DBError
     */
    public static function authenticate(string $email_or_username, string $password): self|bool
    {
        $query = 'select * from users where (username = :u or email = :e)';
        $s = self::db()->prepare($query);
        $s->setFetchMode(PDO::FETCH_CLASS, self::class);
        $s->bindValue(':u', $email_or_username);
        $s->bindValue(':e', $email_or_username);
        $s->execute();
        $user = $s->fetch();
        if ($user) {
            if (password_verify($password, $user->password)) {
                if (!SessionUserAuth::isLoggedIn()) {
                    self::login($user->id);
                }
                return $user;
            }
        }
        return false;
    }

    /**
     * @throws Exception
     * @throws DBError
     */
    public static function loginFromCookie(): self|bool
    {
        $cookie = $_COOKIE['remember'] ?? false;
        if ($cookie) {
            $token = new Token($cookie);
            $token = $token->generateHash();
            $remembered = RememberedLogins::findOne($token);
            if (!$remembered->hasExpired()) {
                self::login($remembered->user_id);
                return self::findOne($remembered->user_id);
            }
        }
        return false;
    }

    /**
     * @throws DBError
     */
    protected static function login(int $id)
    {
        $t = new Token();
        $token = $t->generateHash();
        $s = self::db()->prepare('insert into active_logins (token, user_id) values (?, ?)');
        $s->execute([$token, $id]);
        SessionUserAuth::login($token);
    }

    /**
     * @throws DBError
     */
    public function rememberLogin(): bool
    {
        $token = new Token();
        $hash = $token->generateHash();
        $exp_time = time() + 60 * 60 * 24 * 30; // 30 days

        $this->exp_time = $exp_time;
        $this->token = $token->getToken();

        $sql = 'insert into remembered_logins (token, user_id, expiry_date) values (:hash, :id, :exp)';
        $s = self::db()->prepare($sql);
        return $s->execute([':hash' => $hash, ':id' => $this->id, ':exp' => date('Y-m-d H:i:s', $exp_time)]);
    }

    /**
     * @param string $email
     * @return bool
     * @throws DBError
     */
    public static function emailExists(string $email, ?int $id = null): bool
    {
        return self::_exists('email', $email, $id);
    }

    /**
     * @param string $username
     * @return bool
     * @throws DBError
     */
    public static function usernameExists(string $username, ?int $id = null): bool
    {
        return self::_exists('username', $username, $id);
    }

    /**
     * @throws DBError
     */
    protected static function _exists(string $col, string $val, ?int $id = null): bool
    {
        $sql = "select id from users where $col = :$col";
        if (isset($id)) {
            $sql .= " and id != " . $id;
        }
        $s = self::db()->prepare($sql);
        $s->execute([":$col" => $val]);
        return $s->rowCount() > 0;
    }
}