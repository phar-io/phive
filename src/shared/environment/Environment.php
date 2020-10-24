<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;

abstract class Environment {

    /** @var array */
    protected $server = [];

    /**
     * @return static
     */
    public static function fromSuperGlobals(): self {
        return new static($_SERVER);
    }

    public function __construct(array $server) {
        $this->server = $server;
    }

    /**
     * @throws EnvironmentException
     */
    public function getPathToCommand(string $command): Filename {
        $result = \exec(\sprintf('%s %s', $this->getWhichCommand(), \escapeshellarg($command)), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new EnvironmentException(\sprintf('Command %s not found', $command));
        }
        $resultLines = \explode("\n", $result, 2);

        return new Filename($resultLines[0]);
    }

    abstract public function getHomeDirectory(): Directory;

    abstract public function hasHomeDirectory(): bool;

    abstract public function supportsColoredOutput(): bool;

    public function getWorkingDirectory(): Directory {
        return new Directory(\getcwd());
    }

    public function getProxy(): string {
        if (!$this->hasProxy()) {
            throw new \BadMethodCallException('No proxy set in environment');
        }

        return $this->server['https_proxy'];
    }

    public function hasProxy(): bool {
        return \array_key_exists('https_proxy', $this->server);
    }

    public function hasVariable(string $name): bool {
        return \array_key_exists($name, $this->server);
    }

    public function getVariable(string $name): string {
        if (!$this->hasVariable($name)) {
            throw new \BadMethodCallException(\sprintf('Variable %s is not set in environment', $name));
        }

        return $this->server[$name];
    }

    public function getPhiveCommandPath(): string {
        return $this->server['PHP_SELF'];
    }

    public function getBinaryName(): string {
        return \PHP_BINARY;
    }

    /**
     * @throws ExtensionsMissingException
     */
    public function ensureFitness(): void {
        $this->ensureTimezoneSet();
        $this->ensureRequiredExtensionsLoaded();
        $this->disableXDebug();
    }

    public function getRuntimeString(): string {
        return \sprintf(
            'PHP %s (on %s)',
            $this->getRuntimeVersion(),
            $this->getOperatingSystem()
        );
    }

    public function getRuntimeVersion(): string {
        return \PHP_VERSION;
    }

    public function getOperatingSystem(): string {
        return \sprintf(
            '%s %s',
            \php_uname('s'),
            \php_uname('r')
        );
    }

    public function isInteractive(): bool {
        if (!\function_exists('posix_isatty')) {
            return false;
        }

        return @\posix_isatty(\STDOUT);
    }

    abstract public function getGlobalBinDir(): Directory;

    abstract protected function getWhichCommand(): string;

    private function disableXDebug(): void {
        if (!\extension_loaded('xdebug')) {
            return;
        }
        \ini_set('xdebug.scream', 'off');
        \ini_set('xdebug.max_nesting_level', '8192');
        \ini_set('xdebug.show_exception_trace', 'off');
        // since `xdebug_disable` got removed in Xdebug 3 we have to check for its existance
        if (function_exists('xdebug_disable')) {
            xdebug_disable();
        }
    }

    private function ensureTimezoneSet(): void {
        if (!\ini_get('date.timezone')) {
            \date_default_timezone_set('UTC');
        }
    }

    /**
     * @throws ExtensionsMissingException
     */
    private function ensureRequiredExtensionsLoaded(): void {
        $required = ['dom', 'mbstring', 'pcre', 'curl', 'phar'];
        $missing  = [];

        foreach ($required as $test) {
            if (!\extension_loaded($test)) {
                $missing[] = \sprintf('ext/%s not installed/enabled', $test);
            }
        }

        if (\count($missing)) {
            throw new ExtensionsMissingException($missing);
        }
    }
}
