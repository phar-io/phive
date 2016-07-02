<?php
namespace PharIo\Phive;

class WindowsEnvironment extends Environment {

    /**
     * @return bool
     */
    public function hasHomeDirectory() {
        return array_key_exists('HOMEDRIVE', $this->server) && array_key_exists('HOMEPATH', $this->server);
    }

    /**
     * @return Directory
     */
    public function getHomeDirectory() {
        if (!$this->hasHomeDirectory()) {
            throw new \BadMethodCallException('No home directory set in environment');
        }
        return new Directory($this->server['HOMEDRIVE'] . $this->server['HOMEPATH']);
    }

    /**
     * @return string
     */
    protected function getWhichCommand() {
        return 'where.exe';
    }

    /**
     * @return bool
     */
    public function supportsColoredOutput() {
        return false;
    }
}
