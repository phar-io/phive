<?php
namespace PharIo\Phive\Cli;

use PharIo\Phive\Environment;
use PharIo\Phive\ErrorException;
use PharIo\Phive\Exception;
use PharIo\Phive\ExtensionsMissingException;
use PharIo\Phive\PhiveVersion;

class Runner {

    const RC_OK = 0;
    // OUTDATED PHP VERSION = 1
    const RC_EXT_MISSING = 2;
    const RC_UNKNOWN_COMMAND = 3;
    const RC_ERROR = 4;
    const RC_BUG_FOUND = 5;

    /**
     * @var CommandLocator
     */
    private $locator;

    /**
     * @var Output
     */
    private $output;

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param CommandLocator $locator
     * @param Output         $output
     * @param PhiveVersion   $version
     * @param Environment    $env
     * @param Request        $request
     */
    public function __construct(
        CommandLocator $locator, Output $output, PhiveVersion $version, Environment $env, Request $request
    ) {
        $this->locator = $locator;
        $this->output = $output;
        $this->version = $version;
        $this->environment = $env;
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function run() {
        try {
            $this->environment->ensureFitness();
            $this->setupRuntime();
            $this->showHeader();
            $this->locator->getCommandForRequest($this->request)->execute();
            $this->showFooter();
            return self::RC_OK;
        } catch (ExtensionsMissingException $e) {
            $this->output->writeError(
                sprintf(
                    "Your environment is not ready to run phive due to the following reason(s):\n\n          %s\n",
                    join("\n          ", $e->getMissing())
                )
            );
            return self::RC_EXT_MISSING;
        } catch (CommandLocatorException $e) {
            if ($e->getCode() == CommandLocatorException::UnknownCommand) {
                $this->output->writeError(
                    sprintf("Unknown command '%s'\n\n", $this->request->getCommand())
                );
                return self::RC_UNKNOWN_COMMAND;
            } else {
                $this->showError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
                return self::RC_ERROR;
            }
        } catch (Exception $e) {
            $this->output->writeError($e->getMessage());
            $this->showFooter();
            return self::RC_ERROR;
        } catch (\Exception $e) {
            $this->showError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
            return self::RC_BUG_FOUND;
        } catch (\Throwable $t) {
            $this->showError($t->getMessage(), $t->getFile(), $t->getLine(), $t->getTrace());
            return self::RC_BUG_FOUND;
        }
    }

    /**
     * @param string     $error
     * @param string     $file
     * @param int        $line
     * @param array|null $trace
     */
    private function showError($error, $file, $line, array $trace = null) {

        $baseLen = strlen(realpath(__DIR__ . '/../../..')) + 1;

        $message = [$error];
        $message[] = '';
        $message[] = sprintf(
            '#0 %s(%d)',
            substr($file, $baseLen),
            $line
        );
        if ($trace != null) {
            foreach ($trace as $pos => $step) {
                $file = 'unknown file';
                if (isset($step['file'])) {
                    $file = substr($step['file'], $baseLen);
                }
                $message[] = sprintf(
                    '#%d %s(%d): %s%s%s()',
                    $pos + 1,
                    $file,
                    isset($step['line']) ? $step['line'] : 0,
                    isset($step['class']) ? $step['class'] : '',
                    isset($step['type']) ? $step['type'] : '',
                    $step['function']
                );
            }
            $message[] = sprintf('#%d {main}', count($trace) + 1);
        }
        $this->output->writeError(
            sprintf(
                file_get_contents(__DIR__ . '/error.txt'),
                join("\n          ", $message),
                $this->environment->getRuntimeString(),
                $this->version->getVersion()
            )
        );
    }

    /**
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param string $line
     * @param array  $context
     *
     * @throws ErrorException
     */
    public function errorHandler($code, $message, $file, $line, array $context) {
        throw new ErrorException($message, $code, 1, $file, $line, $context);
    }

    public function shutdownHandler() {
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        $this->showError($error['message'], $error['file'], $error['line'], debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    private function setupRuntime() {
        error_reporting(0);
        ini_set('display_errors', false);
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'shutdownHandler']);
    }

    private function showHeader() {
        $this->output->writeText($this->version->getVersionString() . "\n");
    }

    private function showFooter() {
        $this->output->writeText("\n");
    }

}
