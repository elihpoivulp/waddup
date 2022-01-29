<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Exceptions\CSRFException;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Models\User;
use Waddup\Session\Session;
use Waddup\Utils\Mail;
use Waddup\Utils\Token;

class Forgot extends Controller
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $this->view->render('forgot.twig');
    }

    /**
     * @throws PageNotFound
     * @throws CSRFException
     * @throws DBError
     */
    public function send()
    {
        if ($this->request->isPost()) {
            $email = $this->request->getBody()['email'];
            $error = false;
            if ($user = User::findByEmail($email)) {
                if ($user->storePassReset()) {
                    $mail = new Mail();
                    try {
                        $url = trim_slashes(Request::getBaseURL()) . '/password/reset/' . $user->password_reset_token;
                        $text = <<<EOL
Please click on the following URL to reset your password: $url
EOL;
                        $html = <<<EOL
Please click <a href="$url">here</a> to reset your password.
EOL;
                        $mail->send($email, 'Password Reset', $text, $html);
                    } catch (Exception) {
                        $error = true;
                    };
                } else {
                    $error = true;
                }
            }
            if ($error) {
                sleep(2);
                Session::setFlash('forgot', [
                    'class' => 'red',
                    'showIcon' => 'exclamation circle',
                    'message' => 'An error has occurred. Email could not be sent. Please try again later.'
                ]);
            } else {
                Session::setFlash('forgot', [
                    'class' => 'success',
                    'showIcon' => 'exclamation circle',
                    'message' => 'An email containing password reset URL has been sent to the provided email.'
                ]);
            }
            Response::redirect('forgot');
        } else {
            Response::show404();
        }
    }

    /**
     * @throws PageNotFound
     * @throws DBError
     * @throws Exception
     */
    public function resetAction()
    {
        if (array_key_exists('token', $this->params)) {
            $token = $this->params['token'];
            $user = $this->getUser($token);
            if ($user) {
                $this->view->render('pwd-reset.twig', ['token' => $token]);
            }
        } else {
            Response::show404();
        }
    }

    /**
     * @throws PageNotFound
     * @throws CSRFException
     * @throws DBError
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getBody();
            $token = $data['token'];
            $user = $this->getUser($token);
            if ($user->resetPassword($data['password'])) {
                Session::setFlash('log', [
                    'class' => 'success',
                    'showIcon' => 'check circle',
                    'message' => 'Password has been reset successfully! Please login with your new password.'
                ]);
                Response::redirect('login');
            } else {
                Response::redirect('password/reset/' . $token);
            }
        } else {
            Response::show404();
        }
    }

    /**
     * @throws DBError
     * @throws Exception
     */
    protected function getUser(string $token): ?User
    {
        $user = User::findByPwdReset($token);
        if ($user) {
            return $user;
        } else {
            $this->view->render('error.twig', [
                'data' => ['message' => 'Invalid token']
            ]);
        }
        return null;
    }
}