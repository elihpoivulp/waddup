<?php

namespace Source\App\Controllers;

use Source\Core\Controller;

class Home extends Controller
{
    public function indexAction()
    {
        echo 'Hello from index!';
    }

    protected function before(): bool
    {
        echo 'You must be logged in!';
        return false;
    }
}