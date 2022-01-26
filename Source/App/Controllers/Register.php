<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Exceptions\DBError;
use Waddup\Models\User;
use Waddup\Session\Session;

class Register extends Controller
{
    private string $template_namespace = 'profile';

    /**
     * @throws Exception
     */
    public function __construct(View $view, Request $request)
    {
        parent::__construct($view, $request);
        $this->view->setTemplateNamespace($this->template_namespace);
        $this->view->registerTemplatePath(VIEWS_PATH . '/' . $this->template_namespace, $this->template_namespace);
    }

    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $this->view->render('register.twig', [
            'title' => 'Register'
        ]);
    }

    /**
     * @throws DBError
     * @throws \Waddup\Exceptions\PageNotFound
     */
    public function storeAction()
    {
        if ($this->request->isPost()) {
            $user = new User($this->request->getBody());
            if ($user->save()) {
                Session::setFlash('reg_success', [
                    'showProgress' => 'top',
                    'classProgress' => 'success',
                    'displayTime' => 5000,
                    'showIcon' => 'ship',
                    'message' => "Welcome aboard, <strong>@$user->username</strong>",
                    'position' => 'top center',
                    'class' => 'success'
                ]);
                Session::unsetFormErrors();
                Session::unset('form_values');
                $redirect_to = 'profile';
            } else {
                $redirect_to = 'register';
                foreach ($user->errors() as $key => $item) {
                    Session::set($key, $item);
                }
            }
        } else {
            Response::show404();
        }
        Response::redirect($redirect_to);
    }
}