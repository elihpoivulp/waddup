<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\DB\DBConnectionHandler;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Exceptions\ViewFileNotFound;
use Waddup\Models\User;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;
use Waddup\Utils\Token;

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
            'title' => 'Login'
        ]);
    }

    /**
     * @throws DBError
     * @throws PageNotFound
     */
    public function validateAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getBody();
            $user = User::authenticate($data['usermail'], $data['password']);
            if ($user) {
                Response::redirect('profile');
            } else {
                Session::set('form_values', ['usermail' => $data['usermail']]);
                Session::setFormErrors([
                    'usermail' => ['message' => 'Login failed. Please check your username/email or password.']
                ]);
                Response::redirect('login');
            }
        } else {
            Response::show404();
        }
    }
}