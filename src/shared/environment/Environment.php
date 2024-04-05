<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use const PHP_BINARY;
use const PHP_VERSION;
use const STDOUT;
use function array_key_exists;
use function count;
use function date_default_timezone_set;
use function escapeshellarg;
use function exec;
use function explode;
use function extension_loaded;
use function function_exists;
use function getcwd;
use function ini_get;
use function ini_set;
use function php_uname;
use function posix_isatty;
use function sprintf;
use function xdebug_disable;
use BadMethodCallException;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;

abstract class Environment {
    /** @var array */
    protected $server = [];

    public function __construct(array $server) {
        $this->server = $server;
    }

    /**
     * @throws EnvironmentException
     */
    public function getPathToCommand(string $command): Filename {
        $result = exec(sprintf('%s %s', $this->getWhichCommand(), escapeshellarg($command)), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new EnvironmentException(sprintf('Command %s not found', $command));
        }
        $resultLines = explode("\n", $result, 2);

        return new Filename($resultLines[0]);
    }

    abstract public function getHomeDirectory(): Directory;

    abstract public function hasHomeDirectory(): bool;

    public function getPhiveHomeVariable(): string {
        return $this->getVariable('PHIVE_HOME');
    }

    public function hasPhiveHomeVariable(): bool {
        return $this->hasVariable('PHIVE_HOME');
    }

    abstract public function supportsColoredOutput(): bool;

    public function getWorkingDirectory(): Directory {
        return new Directory(getcwd());
    }

    public function getProxy(): string {
        if (!$this->hasProxy()) {
            throw new BadMethodCallException('No proxy set in environment');
        }

        return $this->server['https_proxy'];
    }

    public function hasProxy(): bool {
        return array_key_exists('https_proxy', $this->server);
    }

    public function hasVariable(string $name): bool {
        return array_key_exists($name, $this->server);
    }

    public function getVariable(string $name): string {
        if (!$this->hasVariable($name)) {
            throw new BadMethodCallException(sprintf('Variable %s is not set in environment', $name));
        }

        return $this->server[$name];
    }

    public function getPhiveCommandPath(): string {
        return $this->server['PHP_SELF'];
    }

    public function getBinaryName(): string {
        return PHP_BINARY;
    }

    /**
     * @throws FeatureMissingException
     */
    public function ensureFitness(): void {
        $this->ensureTimezoneSet();
        $this->ensureRequiredFeaturesAvailable();
        $this->disableXDebug();
    }

    public function getRuntimeString(): string {
        return sprintf(
            'PHP %s (on %s)',
            $this->getRuntimeVersion(),
            $this->getOperatingSystem()
        );
    }

    public function getRuntimeVersion(): string {
        return PHP_VERSION;
    }

    public function getOperatingSystem(): string {
        return sprintf(
            '%s %s',
            php_uname('s'),
            php_uname('r')
        );
    }

    public function isInteractive(): bool {
        if (!function_exists('posix_isatty')) {
            return false;
        }

        return @posix_isatty(STDOUT);
    }

    abstract public function getGlobalBinDir(): Directory;

    abstract protected function getWhichCommand(): string;

    private function disableXDebug(): void {
        if (!extension_loaded('xdebug')) {
            return;
        }
        ini_set('xdebug.scream', 'off');
        ini_set('xdebug.max_nesting_level', '8192');
        ini_set('xdebug.show_exception_trace', 'off');

        // since `xdebug_disable` got removed in Xdebug 3 we have to check for its existence
        if (function_exists('xdebug_disable')) {
            xdebug_disable();
        }
    }

    private function ensureTimezoneSet(): void {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }

    private function ensureRequiredFeaturesAvailable(): void {
        $extensions = ['dom', 'mbstring', 'pcre', 'curl', 'phar', 'libxml'];
        $functions  = ['exec', 'php_uname', 'escapeshellarg'];
        $missing    = [];

        foreach ($extensions as $test) {
            if (!extension_loaded($test)) {
                $missing[] = sprintf('ext/%s not installed/enabled', $test);
            }
        }

        foreach ($functions as $test) {
            if (!function_exists($test)) {
                $missing[] = sprintf('function %s not available', $test);
            }
        }

        if (count($missing)) {
            throw new FeatureMissingException($missing);
        }
    }
}
