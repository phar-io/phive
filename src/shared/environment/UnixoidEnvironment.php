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

        // ConsoleOutput::writeText() uses STDOUT, too.
        if (! posix_isatty(STDOUT)) {
            return false;
        }

        try {
            $tput      = $this->getPathToCommand('tput');
            $exit_code = null;
            $result    = [];
            exec("{$tput} colors", $result, $exit_code);
            if (0 !== (int)$exit_code) {
                return false;
            }
            if (isset($result[0]) && 8 === (int)$result[0]) {
                return true;
            }
        } catch (EnvironmentException $e) {}

        return false;
    }

    /**
     * @return string
     */
    protected function getWhichCommand() {
        return 'which';
    }
}
