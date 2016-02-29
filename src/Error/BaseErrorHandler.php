<?php

namespace Strata\Error;

use Strata\Strata;
use Strata\Core\StrataConfigurableTrait;
use Exception;

/**
 * The base error handler of Strata code. This is very much so based on
 * CakePHP's method.
 * @link https://github.com/cakephp/cakephp/blob/master/src/Error/BaseErrorHandler.php
 */
class BaseErrorHandler
{
    use StrataConfigurableTrait;

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
        $useDebugger = $this->useStrataDebugger();

        if ($debugLevel > 0 && Strata::isDev() && !Strata::isCommandLineInterface() && !is_admin() && $useDebugger) {
            error_reporting(0); // we'll report it ourselves
            set_error_handler(array($this, 'handleError'), $debugLevel);
            set_exception_handler(array($this, 'wrapAndHandleException'));
            add_action('shutdown', array($this, 'shutdown'));
        }
    }

    public function shutdown()
    {
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
        $data = array(
            'type' => "Error",
            'code' => $code,
            'error' => $error,
            'description' => $description,
            'file' => $file,
            'line' => $line,
            'context' => $context,
        );

        $this->logErrorData($data);
        $this->displayErrorData($data);
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
        $data = array(
            'type' => "Fatal Error",
            'code' => $code,
            'description' => $description,
            'file' => $file,
            'line' => $line,
            'error' => 'Fatal Error',
        );

        $this->logErrorData($data);
        $this->displayErrorData($data);
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
        $data = array(
            'type' => "Exception",
            'code' => $exception->getCode(),
            'description' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'error' => 'Fatal Error',
        );

        $this->displayExceptionData($data);
        $this->logExceptionData($data);
    }

    /**
     * Displays an error.
     * @param array $data Array of error data.
     */
    protected function displayErrorData($data)
    {
        $this->clearBuffer();
        $debug = new ErrorDebugger();
        $debug->setErrorData($data);
        echo $debug->compile();
        exit();
    }

    /**
     * Displays an exception.
     * @param array $data Array of error data.
     */
    protected function displayExceptionData($data)
    {
        $this->clearBuffer();
        $debug = new ErrorDebugger();
        $debug->setErrorData($data);
        echo $debug->compile();
        exit();
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

        return E_ALL;
    }

    private function getFatalErrorsTypes()
    {
        return array(
            E_USER_ERROR,
            E_ERROR,
            E_PARSE,
        );
    }

    private function clearBuffer()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }

    private function useStrataDebugger()
    {
        if ($this->hasConfig("error.use_debugger")) {
            return (bool)$this->getConfig("error.use_debugger");
        }

        return true;
    }

}
