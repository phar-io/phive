<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

use PharIo\Phive\Environment;
use PharIo\Phive\ErrorException;
use PharIo\Phive\Exception;
use PharIo\Phive\ExtensionsMissingException;
use PharIo\Phive\PhiveContext;
use PharIo\Phive\PhiveVersion;

class Runner {
    public const RC_OK = 0;

    // OUTDATED PHP VERSION = 1
    public const RC_EXT_MISSING = 2;

    public const RC_UNKNOWN_COMMAND = 3;

    public const RC_ERROR = 4;

    public const RC_PARAM_ERROR = 5;

    public const RC_BUG_FOUND = 10;

    /** @var CommandLocator */
    private $locator;

    /** @var Output */
    private $output;

    /** @var PhiveVersion */
    private $version;

    /** @var Environment */
    private $environment;

    /** @var Request */
    private $request;

    public function __construct(
        CommandLocator $locator,
        Output $output,
        PhiveVersion $version,
        Environment $env,
        Request $request
    ) {
        $this->locator     = $locator;
        $this->output      = $output;
        $this->version     = $version;
        $this->environment = $env;
        $this->request     = $request;
    }

    public function run(): int {
        try {
            $this->ensureFitness();
            $this->setupRuntime();
            $this->execute();

            return self::RC_OK;
        } catch (RunnerException $e) {
            $this->showException($e);

            return $e->getCode();
        } catch (ErrorException $e) {
            $this->showErrorWithTrace($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());

            return self::RC_BUG_FOUND;
        } catch (Exception $e) {
            $this->showException($e);

            return self::RC_ERROR;
        } catch (\Exception $e) {
            $this->showErrorWithTrace($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());

            return self::RC_BUG_FOUND;
        } catch (\Throwable $t) {
            $this->showErrorWithTrace($t->getMessage(), $t->getFile(), $t->getLine(), $t->getTrace());

            return self::RC_BUG_FOUND;
        }
    }

    /**
     * @throws ErrorException
     */
    public function errorHandler(int $code, string $message, string $file, string $line, array $context): void {
        throw new ErrorException($message, $code, 1, $file, $line, $context);
    }

    public function shutdownHandler(): void {
        $error = \error_get_last();

        if ($error === null) {
            return;
        }
        $this->showErrorWithTrace($error['message'], $error['file'], $error['line'], \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    private function showErrorWithTrace(string $error, string $file, int $line, array $trace = null): void {
        $baseLen = \strlen(\realpath(__DIR__ . '/../../..')) + 1;

        $message   = [$error];
        $message[] = '';
        $message[] = \sprintf(
            '#0 %s(%d)',
            \substr($file, $baseLen),
            $line
        );

        if (\is_array($trace)) {
            foreach ($trace as $pos => $step) {
                $file = 'unknown file';

                if (isset($step['file'])) {
                    $file = \substr($step['file'], $baseLen);
                }
                $message[] = \sprintf(
                    '#%d %s(%d): %s%s%s()',
                    $pos + 1,
                    $file,
                    $step['line'] ?? 0,
                    $step['class'] ?? '',
                    $step['type'] ?? '',
                    $step['function']
                );
            }
            $message[] = \sprintf('#%d {main}', \count($trace) + 1);
        }
        $this->output->writeError(
            \sprintf(
                \file_get_contents(__DIR__ . '/error.txt'),
                \implode("\n          ", $message),
                $this->environment->getRuntimeString(),
                $this->version->getVersion()
            )
        );
    }

    private function setupRuntime(): void {
        \error_reporting(-1);
        \ini_set('display_errors', 'off');
        \set_error_handler([$this, 'errorHandler']);
        \register_shutdown_function([$this, 'shutdownHandler']);
    }

    private function showHeader(): void {
        $this->output->writeText($this->version->getVersionString() . "\n");
    }

    private function showFooter(): void {
        $this->output->writeText("\n");
    }

    private function ensureFitness(): void {
        try {
            $this->environment->ensureFitness();
        } catch (ExtensionsMissingException $e) {
            throw new RunnerException(
                \sprintf(
                    "Your environment is not ready to run phive due to the following reason(s):\n\n          %s\n",
                    \implode("\n          ", $e->getMissing())
                ),
                self::RC_EXT_MISSING
            );
        }
    }

    private function parseRequest(): ?string {
        try {
            $options = $this->request->parse(new PhiveContext());

            if ($options->hasArgument(0)) {
                return $options->getArgument(0);
            }

            return '';
        } catch (RequestException $e) {
            throw new RunnerException(
                $e->getMessage(),
                self::RC_PARAM_ERROR
            );
        }
    }

    private function execute(): void {
        $command = $this->parseRequest();

        try {
            $this->showHeader();
            $this->locator->getCommand($command)->execute();
            $this->showFooter();
        } catch (CommandLocatorException $e) {
            if ($e->getCode() === CommandLocatorException::UnknownCommand) {
                throw new RunnerException(
                    \sprintf("Unknown command '%s'", $command),
                    self::RC_UNKNOWN_COMMAND
                );
            }

            throw $e;
        } catch (RequestException $e) {
            throw new RunnerException(
                \sprintf(
                    "Error while processing arguments to command '%s':\n%s",
                    $command,
                    $e->getMessage()
                ),
                self::RC_PARAM_ERROR
            );
        }
    }

    private function showException(\Exception $e): void {
        foreach (\explode("\n", $e->getMessage()) as $line) {
            $this->output->writeError($line);
        }
        $this->showFooter();
    }
}
