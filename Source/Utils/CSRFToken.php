<?php

namespace Waddup\Utils;

use Waddup\Session\Session;

class CSRFToken
{
    /**
     * @throws \Exception
     */
    public static function generate(): string
    {
        if (!Session::exists('_csrf')) {
            $token = new Token(bytes: 32);
            Session::set('_csrf', $token->generateHash());
        }
        return Session::get('_csrf');
    }

    public static function validate(string $token): bool
    {
        if (Session::exists('_csrf') && Session::get('_csrf') === $token) {
            Session::unset('_csrf');
            return true;
        }
        return false;
    }
}