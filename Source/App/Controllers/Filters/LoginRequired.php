<?php

namespace Waddup\App\Controllers\Filters;

use Waddup\Core\Controller;
use Waddup\Core\Response;
use Waddup\Exceptions\DBError;
use Waddup\Models\LoggedInUser;
use Waddup\Models\User;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;

abstract class LoginRequired extends Controller
{
    protected User $user;

    /**
     * @throws DBError
     */
    protected function before()
    {
        $user = false;
        if (SessionUserAuth::isLoggedIn()) {
            $user = LoggedInUser::getLoggedInUser(SessionUserAuth::getToken());
        } else if (isset($_COOKIE['remember'])) {
            $user = User::loginFromCookie();
        }
        if (!$user) {
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
        } else {
            $this->user = $user;
        }
        return true;
    }
}