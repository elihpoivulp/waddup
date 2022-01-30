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
use Waddup\Utils\Token;

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
     * @throws PageNotFound
     * @throws CSRFException
     * @throws Exception
     */
    public function updatePhotoAction()
    {
        if ($this->request->isPost()) {
            $name = $_FILES['profile-photo']['name'];
            $ext = strtolower(substr($name, strrpos($name, '.') + 1));
            $token = new Token();
            $new_name = $token->getToken() . ".$ext";
            $upload = UPLOADS_PATH . "/$new_name";
            $user = $this->getUser();
            if ($user->updateProfilePhoto($new_name)) {
                Session::setFlash('settings', [
                    'class' => 'success',
                    'showIcon' => 'check circle',
                    'message' => 'Profile photo has been updated'
                ]);
               if (!move_uploaded_file($_FILES['profile-photo']['tmp_name'], $upload)) {
                   Session::setFlash('settings', [
                       'class' => 'orange',
                       'showIcon' => 'exclamation circle',
                       'message' => 'Cannot upload image.'
                   ]);
               }
            } else {
                Session::setFlash('settings', [
                    'class' => 'red',
                    'showIcon' => 'exclamation circle',
                    'message' => 'An error has occurred. Please try again later.'
                ]);
            }
            Response::redirect('/profile/settings');
        } else {
            Response::show404();
        }
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
                $user = $this->getUser();
                if ($this->$method($user, $this->request->getBody())) {
                    Session::setFlash('settings', [
                        'class' => 'success',
                        'showIcon' => 'check circle',
                        'message' => $type . ' has been updated!'
                    ]);
                } else {
                    Session::setFlash('settings', [
                        'class' => 'orange',
                        'showIcon' => 'exclamation circle',
                        'message' => 'Failed to update ' . $type
                    ]);
                }
            }
            Response::redirect('profile/settings');
        } else {
            Response::show404();
        }
    }

    /**
     * @throws DBError
     */
    protected function getUser(): User
    {
        return LoggedInUser::getLoggedInUser(SessionUserAuth::getToken()) ?? User::loginFromCookie();
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