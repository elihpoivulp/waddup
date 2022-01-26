<?php

namespace Waddup\App\Controllers\Filters;

use Waddup\Models\User;

class LoginRequired extends \Waddup\Core\Controller
{
    protected User $user;

    protected function before()
    {

    }
}