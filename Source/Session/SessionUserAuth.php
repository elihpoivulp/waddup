<?php

namespace Waddup\Session;

use Waddup\Config\Config;

class SessionUserAuth
{
    public static function login(string $token): bool
    {
        if (!self::isLoggedIn()) {
            session_regenerate_id();  // prevent session fixation
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
            Session::unset('user');
        }
        return true;
    }

    public static function isLoggedIn(): bool
    {
        return !is_null(self::getToken()) && Session::exists('user') && self::getToken() === Session::get('user')['token'] && self::lastLoginIsRecent();
    }

    static public function getToken(): ?string
    {
        if (Session::exists('user')) {
            return Session::get('user')['token'];
        }
        return null;
    }

    static private function lastLoginIsRecent(): bool
    {
        if (!Session::exists('user') || Session::get('user')['last_login'] + Config::MAX_LOGIN_AGE < time()) {
            return false;
        }
        return true;
    }
}