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

class Posts extends LoginRequired
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
}