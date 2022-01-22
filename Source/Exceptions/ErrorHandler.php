<?php

namespace Waddup\Exceptions;

use ErrorException;
use Exception;
use Waddup\App\Controllers\Error;
use Waddup\Config\Config;
use Waddup\Core\Request;
use Waddup\Core\View;

/**
 * Error and exception handler
 * - Converts errors to exceptions so that errors and exceptions can be caught
 * by only one handler.
 */
class ErrorHandler
{
    /**
     * Convert all Errors to Exceptions by throwing ErrorException
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     * @throws ErrorException
     */
    public static function errHandler(int $level, string $message, string $file, int $line)
    {
        if (error_reporting() !== 0) {
            throw new ErrorException($message, 0, $file, $line);
        }
    }

    /**
     * Handles exceptions thrown
     * @param Exception $exception
     * @return void
     */
    public static function excHandler(Exception $exception)
    {
        $code = $exception->getCode();
        if ($code !== 404) {
            $code = 500;
        }
        http_response_code($code);
        if (Config::$DEBUG) {
            $msg = '<h1>Fatal Error</h1>';
            $msg .= self::getMessage($exception);
            exit($msg);
        } else {
            $file_name = date('Y-m-d') . '.txt';
            if (!file_exists(LOGS_PATH)) {
                echo 'folder does not exist' . '<br>';
                mkdir(LOGS_PATH, 0777, true);
            }
            if (!is_writable(LOGS_PATH)) {
                echo 'chmod' . '<br>';
                chmod(LOGS_PATH, 0777);
            }

            $situation = 'An Error has occurred.';
            if ($code === 404) {
                $situation = 'Page not found.';
                ini_set('error_log', LOGS_PATH . "/$file_name");
                error_log(strip_tags(self::getMessage($exception) . "\n\n"));
            }

            $e = new Error(new View(), new Request());
            $e->showAction(['message' => $situation]);
        }
    }

    protected static function getMessage(Exception $exception): string
    {
        $exc_class = get_class($exception);
        $message = $exception->getMessage();
        $trace = $exception->getTraceAsString();
        $file = $exception->getFile();
        $line = $exception->getLine();
        return <<<EOT
<p><strong>Uncaught Exception:</strong> $exc_class</p>
<p><strong>Message:</strong> $message</p>
<p><strong>Stack trace:</strong></p>
<pre>
    $trace
</pre>
<p><strong>Thrown in: </strong><em>$file</em> on line <em>$line</em></p>
EOT;
    }
}