<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\Request;
use Waddup\Core\View;
use Waddup\Exceptions\ViewFileNotFound;
use Waddup\Models\Post;

class Home extends Controller
{
    private string $template_namespace = 'homepage';

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
        $this->view->render('index.twig', [
            'title' => 'Home',
            'posts' => Post::getAll()
        ]);
    }

//    protected function before(): bool
//    {
//        echo 'You must be logged in!';
//        return false;
//    }
}