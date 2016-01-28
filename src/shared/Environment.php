<?php
namespace PharIo\Phive;

class Environment {

    /**
     * @var array
     */
    private $server = [];

    /**
     * @param array $server
     */
    public function __construct(array $server) {
        $this->server = $server;
    }

    /**
     * @return Directory
     */
    public function getHomeDirectory() {
        if (!$this->hasHomeDirectory()) {
            throw new \BadMethodCallException('No home directory set in environment');
        }
        return new Directory($this->server['HOME']);
    }

    /**
     * @return bool
     */
    public function hasHomeDirectory() {
        return array_key_exists('HOME', $this->server);
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return (new Directory(getcwd()))->child('tools');
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

    /**
     * @return string
     */
    public function getBinaryName() {
        return $this->server['_'];
    }

    /**
     * @throws ExtensionsMissionException
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
     * @throws ExtensionsMissionException
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
            throw new ExtensionsMissionException($missing);
        }
    }

}
