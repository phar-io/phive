<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;

class UnixoidEnvironment extends Environment {

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @param array $server
     * @param Executor $executor
     */
    public function __construct(array $server, Executor $executor) {
        parent::__construct($server);
        $this->executor = $executor;
    }

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
     * @throws \PharIo\Phive\EnvironmentException
     */
    public function supportsColoredOutput() {
        if (!$this->isInteractive()) {
            return false;
        }

        if (!array_key_exists('TERM', $this->server)) {
            return false;
        }

        $tput = $this->getPathToCommand('tput');
        $commandResult = $this->executor->execute($tput, 'colors');
        if (!$commandResult->isSuccess()) {
            return false;
        }
        return (int)$commandResult->getOutput()[0] >= 8;
    }

    /**
     * @return string
     */
    protected function getWhichCommand() {
        return 'which';
    }

    /**
     * @return UnixoidEnvironment
     */
    public static function fromSuperGlobals() {
        return new static($_SERVER, new Executor());
    }

    /**
     * @return Directory
     */
    public function getGlobalBinDir() {
        return new Directory('/usr/bin');
    }
}
