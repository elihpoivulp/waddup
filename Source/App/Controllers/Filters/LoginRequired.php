<?php

namespace Waddup\App\Controllers\Filters;

use Waddup\Core\Controller;
use Waddup\Core\Response;
use Waddup\Models\User;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;

class LoginRequired extends Controller
{
    protected User $user;

    protected function before()
    {
        if (!SessionUserAuth::isLoggedIn()) {
            Session::setFlash('log', [
                'showIcon' => 'exclamation circle',
                'message' => "You must login first.",
                'class' => 'info'
            ]);
            $next = '';
            if ($uri = $this->params['request_uri']) {
                $next = '?next=' . urlencode($uri);
            }
            Response::redirect('login' . $next);
        }
    }
}