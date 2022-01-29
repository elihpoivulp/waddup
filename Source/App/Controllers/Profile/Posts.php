<?php

namespace Waddup\App\Controllers\Profile;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\Request;
use Waddup\Core\Response;
use Waddup\Core\View;
use Waddup\Models\Post;

class Posts extends Controller
{
    private string $template_namespace = 'posts';

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
    public function showAction()
    {
        if ($post = Post::findOne($this->params['id'])) {
            $this->view->render('post.twig', [
                'title' => 'Story',
                'post' => $post,
                'show_sidebar' => $_GET['posted'] ?? false
        ]);
        } else {
            Response::show404();
        }
    }
}