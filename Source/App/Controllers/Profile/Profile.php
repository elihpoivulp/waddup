<?php

namespace Waddup\App\Controllers\Profile;

use Exception;
use Waddup\App\Controllers\Filters\LoginRequired;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Exceptions\CSRFException;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Models\LoggedInUser;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;

class Profile extends LoginRequired
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

    public function indexAction()
    {
        $this->view->render('profile.twig', [
            'title' => 'Profile'
        ]);
    }

    /**
     * @throws PageNotFound
     * @throws CSRFException
     * @throws DBError
     */
    public function logoutAction()
    {
        if ($this->request->isPost()) {
            if (SessionUserAuth::isLoggedIn()) {
                Session::setFlash('log', [
                    'showIcon' => 'exclamation circle',
                    'message' => "You have been logged out.",
                    'class' => 'info'
                ]);
                LoggedInUser::logsOut(SessionUserAuth::getToken());
                SessionUserAuth::logout();
                Response::redirect('login');
            }
        }
        Response::show404();
    }
}