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
     * @param string $command
     *
     * @return Filename
     * @throws EnvironmentException
     */
    public function getPathToCommand($command) {
        $result = exec(sprintf('where.exe %s', $command), $output, $exitCode);
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
        return false;
    }


}
