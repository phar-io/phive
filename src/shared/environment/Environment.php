<?php
namespace PharIo\Phive;

abstract class Environment {

    /**
     * @var array
     */
    protected $server = [];

    /**
     * @param array $server
     */
    public function __construct(array $server) {
        $this->server = $server;
    }

    public static function fromSuperGlobals() {
        return new static($_SERVER);
    }

    /**
     * @param string $command
     *
     * @return Filename
     * @throws EnvironmentException
     */
    public function getPathToCommand($command) {
        $result = exec(sprintf('%s %s', $this->getWhichCommand(), $command), $output, $exitCode);
        if ($exitCode !== 0) {
            throw new EnvironmentException(sprintf('Command %s not found', $command));
        }
        $resultLines = explode("\n", $result);
        return new Filename($resultLines[0]);
    }
    
    /**
     * @return Directory
     */
    abstract public function getHomeDirectory();

    /**
     * @return bool
     */
    abstract public function hasHomeDirectory();

    /**
     * @return bool
     */
    abstract public function supportsColoredOutput();

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return new Directory(getcwd());
    }

    /**
     * @return string
     */
    public function getProxy() {
        if (!$this->hasProxy()) {
            throw new \BadMethodCallException('No proxy set in environment');
        }
        return $this->server['https_proxy'];
    }

    /**
     * @return bool
     */
    public function hasProxy() {
        return array_key_exists('https_proxy', $this->server);
    }

    public function getPhiveCommandPath() {
        return $this->server['PHP_SELF'];
    }

    /**
     * @return string
     */
    public function getBinaryName() {
        return PHP_BINARY;
    }

    /**
     * @throws ExtensionsMissingException
     */
    public function ensureFitness() {
        $this->ensureTimezoneSet();
        $this->ensureRequiredExtensionsLoaded();
        $this->disableXDebug();
    }

    /**
     * @return string
     */
    public function getRuntimeString() {
        return sprintf(
            '%s %s',
            $this->isHHVM() ? 'HHVM' : 'PHP',
            $this->getRuntimeVersion()
        );
    }

    /**
     * @return string
     */
    public function getRuntimeVersion() {
        if ($this->isHHVM()) {
            return HHVM_VERSION;
        }
        return PHP_VERSION;
    }

    /**
     * @return string
     */
    abstract protected function getWhichCommand();

    private function isHHVM() {
        return defined('HHVM_VERSION');
    }

    private function disableXDebug() {
        if (!extension_loaded('xdebug')) {
            return;
        }
        ini_set('xdebug.scream', 0);
        ini_set('xdebug.max_nesting_level', 8192);
        ini_set('xdebug.show_exception_trace', 0);
        xdebug_disable();
    }

    private function ensureTimezoneSet() {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }

    /**
     * @throws ExtensionsMissingException
     */
    private function ensureRequiredExtensionsLoaded() {
        $required = ['dom', 'mbstring', 'pcre', 'curl', 'phar'];
        $missing = [];

        foreach ($required as $test) {
            if (!extension_loaded($test)) {
                $missing[] = sprintf('ext/%s not installed/enabled', $test);
            }
        }

        if (count($missing)) {
            throw new ExtensionsMissingException($missing);
        }
    }

}
