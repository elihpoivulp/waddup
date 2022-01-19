<?php

namespace Source\App\Controllers;

use Exception;
use Source\Core\Controller;
use Source\Exceptions\ViewFileNotFound;

class Home extends Controller
{
    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $this->view->render('test_page.twig', [
            'title' => 'Test page'
        ]);
    }

//    protected function before(): bool
//    {
//        echo 'You must be logged in!';
//        return false;
//    }
}