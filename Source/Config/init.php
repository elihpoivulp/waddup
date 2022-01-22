<?php
/**
 * Configurations on initialization
 */

ob_start();
date_default_timezone_set('Asia/Manila');

// errors
error_reporting(E_ALL);
set_error_handler('\Waddup\Exceptions\ErrorHandler::errHandler');
set_exception_handler('\Waddup\Exceptions\ErrorHandler::excHandler');