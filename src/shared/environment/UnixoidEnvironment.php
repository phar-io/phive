<?php
namespace PharIo\Phive;

class UnixoidEnvironment extends Environment {

    /**
     * @return bool
     */
    public function hasHomeDirectory() {
        return array_key_exists('HOME', $this->server);
    }

    /**
     * @return Directory
     * @throws \PharIo\Phive\DirectoryException
     * @throws \BadMethodCallException
     */
    public function getHomeDirectory() {
        if (!$this->hasHomeDirectory()) {
            throw new \BadMethodCallException('No home directory set in environment');
        }
        return new Directory($this->server['HOME']);
    }

    /**
     * @param string $command
     *
     * @return Filename
     * @throws EnvironmentException
     */
    public function getPathToCommand($command) {
        $result = exec(sprintf('which %s', $command), $output, $exitCode);
        if ($exitCode !== 0) {
            throw new EnvironmentException(sprintf('Command %s not found', $command));
        }
        $resultLines = explode("\n", $result);
        return new Filename($resultLines[0]);
    }

    /**
     * @return bool
     */
    public function supportsColoredOutput() {
        // TODO check with 'tput colors'
        return true;
    }


}
