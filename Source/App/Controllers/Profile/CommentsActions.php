<?php

namespace Waddup\App\Controllers\Profile;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\Response;
use Waddup\Exceptions\CSRFException;
use Waddup\Exceptions\DBError;
use Waddup\Exceptions\PageNotFound;
use Waddup\Models\Comment;
use Waddup\Models\Post;
use Waddup\Session\Session;
use Waddup\Utils\CSRFToken;

class CommentsActions extends Controller
{
    /**
     * @throws PageNotFound
     * @throws DBError
     * @throws Exception
     */
    public function store()
    {
        try {
            if ($this->request->isPost()) {
                $data = $this->request->getBody();
                $c = new Comment($data);
                if ($c->save()) {
                    $sess_data = [
                        'class' => 'success',
                        'showIcon' => 'check circle',
                        'message' => 'Your comment has been posted.'
                    ];
                } else {
                    $sess_data = [
                        'class' => 'warning',
                        'showIcon' => 'exclamation circle',
                        'message' => 'An error has occurred. Please try again later.'
                    ];
                }
                Session::setFlash('comment', $sess_data);
                Response::redirect('posts/' . $data['post_id'] . '?posted=true');
            }
        } catch (CSRFException) {
            Response::show404();
        }
    }
}