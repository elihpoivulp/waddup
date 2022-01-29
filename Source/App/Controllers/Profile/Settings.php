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
use Waddup\Models\Post;
use Waddup\Models\User;
use Waddup\Session\Session;
use Waddup\Session\SessionUserAuth;
use Waddup\Utils\CSRFToken;

class Settings extends LoginRequired
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
        $this->view->render('settings.twig', [
            'title' => 'Profile Settings'
        ]);
    }

    /**
     * @throws DBError
     * @throws PageNotFound
     * @throws CSRFException
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            $valid_updates = ['profile', 'password'];
            $update = $this->params['update'];
            if (!in_array($update, $valid_updates)) {
                Session::setFlash('settings', [
                    'class' => 'red',
                    'showIcon' => 'exclamation circle',
                    'message' => 'Invalid request!'
                ]);
            } else {
                $pref = 'update';
                $type = ucfirst($update);
                $method = $pref.$type;
                $user = LoggedInUser::getLoggedInUser(SessionUserAuth::getToken()) ?? User::loginFromCookie();
                if ($this->$method($user, $this->request->getBody())) {
                    Session::setFlash('settings', [
                        'class' => 'success',
                        'showIcon' => 'check circle',
                        'message' => $type . ' has been updated!'
                    ]);
                }
            }
            Response::redirect('/profile/settings');
        } else {
            Response::show404();
        }
    }

    /**
     * @throws DBError
     */
    protected function updateProfile(User $user, array $data): bool
    {
        foreach ($data as $prop => $item) {
            $user->editProp($prop, $item);
        }
        return $user->update();
    }

    /**
     * @throws DBError
     */
    protected function updatePassword(User $user, array $data): bool
    {
        foreach ($data as $prop => $item) {
            $user->editProp($prop, $item);
        }
        return $user->update();
    }
}