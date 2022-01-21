<?php

namespace Waddup\App\Controllers\Profile;

use Exception;
use Waddup\Core\Request;
use Waddup\Core\View;

class Profile extends \Waddup\Core\Controller
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

    public function indexAction()
    {
        $this->view->render('profile.twig', [
            'title' => 'Profile'
        ]);
    }
}