<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\App\Controllers\Filters\GuestOnly;
use Waddup\Core\Controller;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Models\User;
use Waddup\Session\Session;

class Register extends GuestOnly
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
        $this->view->render('register.twig', [
            'title' => 'Register'
        ]);
    }

    /**
     * @throws DBError
     * @throws PageNotFound
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
            }
        } else {
            Response::show404();
        }
        Response::redirect($redirect_to);
    }
}