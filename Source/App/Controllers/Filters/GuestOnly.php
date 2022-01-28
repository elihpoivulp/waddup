<?php

namespace Waddup\App\Controllers\Filters;

use Waddup\Core\Controller;
use Waddup\Core\Response;
use Waddup\Exceptions\DBError;
use Waddup\Models\LoggedInUser;
use Waddup\Models\User;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;

abstract class GuestOnly extends Controller
{
    protected function before()
    {
        if (SessionUserAuth::isLoggedIn()) {
            Response::redirect('profile');
        }
    }
}