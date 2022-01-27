<?php

namespace Waddup\Exceptions;

use Exception;

class PageNotFound extends Exception
{
    protected $code = 404;
}