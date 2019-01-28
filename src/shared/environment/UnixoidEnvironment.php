<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;

class UnixoidEnvironment extends Environment {

    /** @var Executor */
    private $executor;

    public static function fromSuperGlobals(): self {
        return new static($_SERVER, new Executor());
    }

    public function __construct(array $server, Executor $executor) {
        parent::__construct($server);
        $this->executor = $executor;
    }

    public function hasHomeDirectory(): bool {
        return \array_key_exists('HOME', $this->server);
    }

    /**
     * @throws \PharIo\Phive\DirectoryException
     * @throws \BadMethodCallException
     */
    public function getHomeDirectory(): Directory {
        if (!$this->hasHomeDirectory()) {
            throw new \BadMethodCallException('No home directory set in environment');
        }

        return new Directory($this->server['HOME']);
    }

    /**
     * @throws EnvironmentException
     */
    public function getPathToCommand(string $command): Filename {
        $result = \exec(\sprintf('which %s', $command), $output, $exitCode);

        if ($exitCode !== 0) {
            throw new EnvironmentException(\sprintf('Command %s not found', $command));
        }
        $resultLines = \explode("\n", $result);

        return new Filename($resultLines[0]);
    }

    /**
     * @throws \PharIo\Phive\EnvironmentException
     */
    public function supportsColoredOutput(): bool {
        if (!$this->isInteractive()) {
            return false;
        }

        if (!\array_key_exists('TERM', $this->server)) {
            return false;
        }

        $tput          = $this->getPathToCommand('tput');
        $commandResult = $this->executor->execute($tput, 'colors');

        if (!$commandResult->isSuccess()) {
            return false;
        }

        return (int)$commandResult->getOutput()[0] >= 8;
    }

    public function getGlobalBinDir(): Directory {
        return new Directory('/usr/local/bin');
    }

    protected function getWhichCommand(): string {
        return 'which';
    }
}
