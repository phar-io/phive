<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function array_key_exists;
use function dirname;
use BadMethodCallException;
use PharIo\FileSystem\Directory;

final class WindowsEnvironment extends Environment {
    /**
     * @return static
     */
    public static function fromSuperGlobals(): self {
        return new self($_SERVER);
    }

    public function hasHomeDirectory(): bool {
        return array_key_exists('HOMEDRIVE', $this->server) && array_key_exists('HOMEPATH', $this->server);
    }

    /**
     * @throws BadMethodCallException
     */
    public function getHomeDirectory(): Directory {
        if (!$this->hasHomeDirectory()) {
            throw new BadMethodCallException('No home directory set in environment');
        }

        return new Directory($this->server['HOMEDRIVE'] . $this->server['HOMEPATH']);
    }

    public function supportsColoredOutput(): bool {
        return array_key_exists('ANSICON', $this->server) || array_key_exists('ConEmuANSI', $this->server);
    }

    public function getGlobalBinDir(): Directory {
        return new Directory(dirname($this->getBinaryName()));
    }

    protected function getWhichCommand(): string {
        return 'where.exe';
    }
}
