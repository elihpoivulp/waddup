<?php

namespace Source\App\Controllers;

use Source\Core\Controller;
use Source\Exceptions\ViewFileNotFound;

class Home extends Controller
{
    /**
     * @throws ViewFileNotFound
     */
    public function indexAction()
    {
        $this->view->render('test_page.php', [
            'title' => 'Test page'
        ]);
    }

//    protected function before(): bool
//    {
//        echo 'You must be logged in!';
//        return false;
//    }
}