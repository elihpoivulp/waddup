<?php

namespace Waddup\Session;

use Waddup\Config\Config;

class SessionUserAuth
{
    public static function login(string $token): bool
    {
        if (!self::isLoggedIn()) {
            session_regenerate_id(true);  // prevent session fixation
            $user_data = [
                'token' => $token,
                'last_login' => time()
            ];
            Session::set('user_data', $user_data);
            return true;
        }
        return false;
    }

    static public function logout(): bool
    {
        if (self::isLoggedIn()) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            session_destroy();
        }
        return true;
    }

    public static function isLoggedIn(): bool
    {
        return !is_null(self::getToken()) && Session::exists('user_data') && self::getToken() === Session::get('user_data')['token'] && self::lastLoginIsRecent();
    }

    static public function getToken(): ?string
    {
        if (Session::exists('user_data')) {
            return Session::get('user_data')['token'];
        }
        return null;
    }

    static private function lastLoginIsRecent(): bool
    {
        if (!Session::exists('user_data') || Session::get('user_data')['last_login'] + Config::MAX_LOGIN_AGE < time()) {
            return false;
        }
        return true;
    }
}