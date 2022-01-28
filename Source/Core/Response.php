<?php

namespace Waddup\Core;

use Waddup\Exceptions\PageNotFound;

class Response
{
    public static function redirect(string $location, int $code = 303)
    {
        header('Location:' . Request::getBaseURL() . trim_slashes($location), true, $code);
        exit;
    }

    /**
     * @throws PageNotFound
     */
    public static function show404()
    {
        throw new PageNotFound('Page does not exist');
    }
}