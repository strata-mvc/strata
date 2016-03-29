<?php

namespace Strata\Error;

use Strata\Strata;
use Strata\Core\StrataConfigurableTrait;
use Strata\Logger\Debugger;

use Exception;

/**
 * The base error handler of Strata code. This is very much so based on
 * CakePHP's method.
 * @link https://github.com/cakephp/cakephp/blob/master/src/Error/BaseErrorHandler.php
 */
class BaseErrorHandler
{
    use StrataConfigurableTrait;

    private $hasError = false;

    /**
     * Checks the passed exception type. If it is an instance of `Error`
     * then, it wraps the passed object inside another Exception object
     * for backwards compatibility purposes.
     *
     * @param \Exception|\Error $exception The exception to handle
     * @return void
     */
    public function wrapAndHandleException($exception)
    {
        if ($exception instanceof Error) {
            $exception = new Exception($exception);
        }

        $this->handleException($exception);
    }

    /**
     * Register the error and exception handlers.
     * @return void
     */
    public function register()
    {
        $debugLevel = $this->getDebugLevel();

        if ($this->shouldBeDebugging()) {
            ob_start();
            register_shutdown_function(function() {
                if (function_exists("is_admin") && is_admin()) {
                    return;
                }

                if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg')) {
                    return;
                }

                $error = error_get_last();
                if (!is_array($error)) {
                    return;
                }

                if (!in_array($error['type'], $this->getFatalErrorsTypes(), true)) {
                    return;
                }
                $this->handleFatalError(
                    $error['type'],
                    $error['message'],
                    $error['file'],
                    $error['line']
                );
            });
            set_error_handler(array($this, 'handleError'), $debugLevel);
            set_exception_handler(array($this, 'wrapAndHandleException'));
            error_reporting($debugLevel);
        }
    }

    /**
     * Set as the default error handler by Strata
     * @param int $code Code of error
     * @param string $description Error description
     * @param string|null $file File on which error occurred
     * @param int|null $line Line that triggered the error
     * @param array|null $context Context
     * @return bool True if error was handled
     */
    public function handleError($code, $description, $file = null, $line = null, $context = null)
    {
        if (function_exists("is_admin") && is_admin()) {
            return;
        }

        if (!function_exists('get_template_directory')) {
            return;
        }

        $data = array(
            'type' => "Error",
            'code' => $code,
            'description' => $description,
            'file' => $file,
            'line' => $line,
            'context' => $context,
            'trace' => Debugger::trace(debug_backtrace()),
        );

        $this->logErrorData($data);
        $this->displayErrorData($data);
        $this->endProcesses();
    }

    /**
     * Logs a fatal error.
     * @param int $code Code of error
     * @param string $description Error description
     * @param string $file File on which error occurred
     * @param int $line Line that triggered the error
     */
    public function handleFatalError($code, $description, $file, $line)
    {
        if (function_exists("is_admin") && is_admin()) {
            return;
        }

        if (!function_exists('get_template_directory')) {
            return;
        }

        $data = array(
            'type' => "Fatal Error",
            'code' => $code,
            'description' => $description,
            'file' => $file,
            'line' => $line,
            'context' => null,
            'trace' => Debugger::trace(debug_backtrace()),
        );

        $this->logErrorData($data);
        $this->displayErrorData($data);
        $this->endProcesses();
    }

    /**
     * Handle uncaught exceptions.
     *
     * Uses a template method provided by subclasses to display errors in an
     * environment appropriate way.
     *
     * @param \Exception $exception Exception instance.
     * @return void
     * @throws \Exception When renderer class not found
     * @see http://php.net/manual/en/function.set-exception-handler.php
     */
    public function handleException(Exception $exception)
    {
        if (function_exists("is_admin") && is_admin()) {
            return;
        }

        if (!function_exists('get_template_directory')) {
            return;
        }

        $data = array(
            'type' => "Exception",
            'code' => $exception->getCode(),
            'description' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => null,
            'trace' => Debugger::trace($exception->getTrace()),
        );

        $this->displayExceptionData($data);
        $this->logExceptionData($data);
        $this->endProcesses();
    }

    /**
     * Displays an error.
     * @param array $data Array of error data.
     */
    protected function displayErrorData($data)
    {
        $this->raiseDebuggerTakeover();

        $debug = new ErrorDebugger();
        $debug->setErrorData($data);
        echo $debug->compile();
    }

    /**
     * Displays an exception.
     * @param array $data Array of error data.
     */
    protected function displayExceptionData($data)
    {
        $this->raiseDebuggerTakeover();

        $debug = new ErrorDebugger();
        $debug->setErrorData($data);
        echo $debug->compile();
    }

    /**
     * Log an error.
     * @param  array  $errorDetails Array of error data.
     */
    protected function logErrorData(array $errorDetails)
    {
        $logger = new ErrorLogger();
        $logger->logError($errorDetails);
    }

    /**
     * Log an exception.
     * @param array $exception
     */
    protected function logExceptionData(array $errorDetails)
    {
        $logger = new ErrorLogger();
        $logger->logError($errorDetails);
    }

    private function getDebugLevel()
    {
        if ($this->hasConfig("error.debug_level")) {
            return $this->getConfig("error.debug_level");
        }

        return -1;
    }

    private function getFatalErrorsTypes()
    {
        return array(
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
        );
    }

    private function clearBuffer()
    {
        while (ob_get_level()) {
            ob_get_clean();
        }
    }

    private function useStrataDebugger()
    {
        if ($this->hasConfig("error.use_debugger")) {
            return (bool)$this->getConfig("error.use_debugger");
        }

        return true;
    }

    private function shouldBeDebugging()
    {
        return $this->useStrataDebugger() &&
                Strata::isDev() &&
               !Strata::isCommandLineInterface();
    }

    private function endProcesses()
    {
        if (function_exists('do_action')) {
            do_action('shutdown');
        }

        die();
    }

    public function raiseDebuggerTakeover()
    {
        $this->hasError = true;
        $this->clearBuffer();

        $controller = Strata::router()->getCurrentController();
        if (!is_null($controller)) {
            $controller->serverError();
        }
    }

}
