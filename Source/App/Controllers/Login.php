<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\DB\DBConnectionHandler;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Exceptions\CSRFException;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Models\User;
use Waddup\Session\Session;

class Login extends Controller
{
    private string $template_namespace = 'profile';

    /**
     * @throws Exception
     */
    public function __construct(array $params, View $view, Request $request)
    {
        parent::__construct($params, $view, $request);
        $this->view->setTemplateNamespace($this->template_namespace);
        $this->view->registerTemplatePath(VIEWS_PATH . '/' . $this->template_namespace, $this->template_namespace);
    }

    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $this->view->render('login.twig', [
            'title' => 'Login',
            'next' => isset($_GET['next']) ? "?next={$_GET['next']}" : ''
        ]);
    }

    /**
     * @throws DBError
     * @throws PageNotFound
     * @throws CSRFException
     */
    public function validateAction()
    {
        if ($this->request->isPost()) {
            $next = $_GET['next'] ?? '';
            $data = $this->request->getBody();
            $user = User::authenticate($data['usermail'], $data['password']);
            if ($user) {
                if ($data['remember']) {
                    if ($user->rememberLogin()) {
                        setcookie('remember', $user->token, $user->exp_time, '/');
                    }
                }
                Session::unsetFormErrors();
                Session::unset('form_values');
                Response::redirect($next ?? 'profile');
            } else {
                Session::set('form_values', [
                    'usermail' => $data['usermail'],
                    'remember' => $data['remember'] ?? false
                ]);
                Session::setFormErrors([
                    'usermail' => ['message' => 'Login failed. Please check your username/email or password.'],
                ]);
                if ($next) {
                    $next = "?next=$next";
                }
                Response::redirect('login' . $next);
            }
        } else {
            Response::show404();
        }
    }
}