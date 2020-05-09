<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

class WindowsEnvironment extends Environment {
    public function hasHomeDirectory(): bool {
        return \array_key_exists('HOMEDRIVE', $this->server) && \array_key_exists('HOMEPATH', $this->server);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getHomeDirectory(): Directory {
        if (!$this->hasHomeDirectory()) {
            throw new \BadMethodCallException('No home directory set in environment');
        }

        return new Directory($this->server['HOMEDRIVE'] . $this->server['HOMEPATH']);
    }

    public function supportsColoredOutput(): bool {
        return \array_key_exists('ANSICON', $this->server) || \array_key_exists('ConEmuANSI', $this->server);
    }

    public function getGlobalBinDir(): Directory {
        return new Directory(\dirname($this->getBinaryName()));
    }

    protected function getWhichCommand(): string {
        return 'where.exe';
    }
}
