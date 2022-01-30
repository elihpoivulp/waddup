<?php

namespace Waddup\App\Controllers\Profile;

use Exception;
use Waddup\App\Controllers\Filters\LoginRequired;
use Waddup\Core\Response;
use Waddup\Exceptions\CSRFException;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Models\Post;
use Waddup\Utils\CSRFToken;

class PostsActions extends LoginRequired
{
    /**
     * @throws PageNotFound
     * @throws DBError
     * @throws Exception
     */
    public function storeAction()
    {
        try {
            if ($this->request->isPost()) {
                $post = new Post($this->request->getBody());
                if ($post->save()) {
                    $data = [
                        'new_csrf' => CSRFToken::generate(),
                    ];
                    echo json_encode($data);
                } else {
                    echo json_encode($post->errors());
                }
            }
        } catch (CSRFException) {
            Response::show404();
        }
    }

    /**
     * @throws PageNotFound
     * @throws Exception
     */
    public function get()
    {
        try {
           if ($this->request->isPost()) {
               $data = $this->request->getBody();
               $posts = Post::getPostsForScroll($data['id']);
               echo json_encode(['new_csrf' => CSRFToken::generate(), 'posts' => $posts]);
           }
        } catch (CSRFException) {
            Response::show404();
        } catch (Exception $e) {
            echo json_encode(['msg' => $e->getMessage()]);
        }
    }
}