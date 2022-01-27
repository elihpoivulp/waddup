<?php

namespace Waddup\Utils;

use Exception;
use Waddup\Config\Config;

class Token
{
    protected ?string $token;

    /**
     * @throws Exception
     */
    public function __construct(?string $token = null, int $bytes = 16)
    {
        if (!is_null($token)) {
            $this->token = $token;
        } else {
            $this->token = bin2hex(random_bytes($bytes));
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function generateHash(): string
    {
        return hash_hmac('sha256', $this->getToken(), Config::SECRET_KEY());
    }
}