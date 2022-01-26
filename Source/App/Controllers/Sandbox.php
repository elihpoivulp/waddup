<?php

namespace Waddup\App\Controllers;

use Waddup\Core\Controller;

class Sandbox extends Controller
{
    public function indexAction()
    {
        $data = [
            'username' => 'someUsername',
            'password' => 'somePassword',
            'otherField' => 'OtherField'
        ];

        $accepted_fields = ['username', 'password'];

        echo '<pre>';
        print_r(array_filter($data, fn ($k) => in_array($k, $accepted_fields), ARRAY_FILTER_USE_KEY));
        echo '</pre>';
    }
}