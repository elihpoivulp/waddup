<?php

namespace Waddup\App\Controllers;

use Exception;
use Waddup\Core\Controller;
use Waddup\Core\Request;
use Waddup\Core\View;
use Waddup\Exceptions\ViewFileNotFound;
use Waddup\Models\Post;

class Error extends Controller
{
    /**
     * @throws Exception
     */
    public function showAction(array $data)
    {
        $this->view->render('error.twig', [
            'title' => 'Oops!',
            'data' => $data
        ]);
    }

//    protected function before(): bool
//    {
//        echo 'You must be logged in!';
//        return false;
//    }
}