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
use function exec;
use BadMethodCallException;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\DirectoryException;
use Throwable;

final class UnixoidEnvironment extends Environment {
    /** @var Executor */
    private $executor;

    /** @var null|string */
    private $whichCommand;

    public static function fromSuperGlobals(): Environment {
        return new self($_SERVER, new Executor());
    }

    public function __construct(array $server, Executor $executor) {
        parent::__construct($server);
        $this->executor = $executor;
    }

    public function hasHomeDirectory(): bool {
        return array_key_exists('HOME', $this->server);
    }

    /**
     * @throws BadMethodCallException
     * @throws DirectoryException
     */
    public function getHomeDirectory(): Directory {
        if (!$this->hasHomeDirectory()) {
            throw new BadMethodCallException('No home directory set in environment');
        }

        return new Directory($this->server['HOME']);
    }

    /**
     * @throws EnvironmentException
     */
    public function supportsColoredOutput(): bool {
        if (!$this->isInteractive()) {
            return false;
        }

        if (!array_key_exists('TERM', $this->server)) {
            return false;
        }

        try {
            $tput          = $this->getPathToCommand('tput');
            $commandResult = $this->executor->execute($tput, 'colors');
        } catch (Throwable $t) {
            return false;
        }

        if (!$commandResult->isSuccess()) {
            return false;
        }

        return (int)$commandResult->getOutput()[0] >= 8;
    }

    public function getGlobalBinDir(): Directory {
        return new Directory('/usr/local/bin');
    }

    protected function getWhichCommand(): string {
        if ($this->whichCommand !== null) {
            return $this->whichCommand;
        }

        foreach (['which', 'type -Pa', 'command -a'] as $tool) {
            exec($tool . ' ls 2>/dev/null', $output, $exitCode);

            if ($exitCode === 0) {
                $this->whichCommand = $tool;

                return $tool;
            }
        }

        return 'which';
    }
}
