<?php
namespace PharIo\Phive;

/**
 * Wrapper for CLI environment variables
 */
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

}
