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
    public static function fromSuperGlobals() {
        return new static($_SERVER);
    }

    public function __construct(array $server) {
        $this->server = $server;
    }

    /**
     * @throws EnvironmentException
     */
    public function getPathToCommand(string $command): Filename {
        $result = \exec(\sprintf('%s %s', $this->getWhichCommand(), $command), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new EnvironmentException(\sprintf('Command %s not found', $command));
        }
        $resultLines = \explode("\n", $result);

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

    public function hasGitHubAuthToken(): bool {
        return \array_key_exists('GITHUB_AUTH_TOKEN', $this->server);
    }

    public function getGitHubAuthToken(): string {
        if (!$this->hasGitHubAuthToken()) {
            throw new \BadMethodCallException('GITHUB_AUTH_TOKEN not set in environment');
        }

        return $this->server['GITHUB_AUTH_TOKEN'];
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
            'PHP %s',
            $this->getRuntimeVersion()
        );
    }

    public function getRuntimeVersion(): string {
        return \PHP_VERSION;
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
        xdebug_disable();
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
