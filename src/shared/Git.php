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

use const PHP_OS;
use function chdir;
use function exec;
use function is_dir;
use function sprintf;
use function strtolower;
use function substr;
use PharIo\FileSystem\Directory;

/**
 * @codeCoverageIgnore
 */
class Git {
    /** @var Directory */
    private $workingDirectory;

    public function __construct(Directory $workingDirectory) {
        $this->workingDirectory = $workingDirectory;
    }

    public function isRepository(Directory $directory): bool {
        return is_dir($directory->asString() . '/.git');
    }

    /**
     * @throws GitException
     */
    public function getMostRecentTag(Directory $directory): string {
        if (!$this->isRepository($directory)) {
            throw new GitException(sprintf('%s is not a git repository', $directory->asString()));
        }
        chdir($directory->asString());
        $tag = @exec('git describe --tags --always --dirty 2>' . $this->getDevNull(), $output, $returnCode);
        chdir($this->workingDirectory->asString());

        if ($returnCode !== 0) {
            throw new GitException('Could not determine most recent tag');
        }

        return $tag;
    }

    private function getDevNull(): string {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win' ? 'nul' : '/dev/null';
    }
}
