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

use function escapeshellarg;
use function exec;
use function sprintf;
use PharIo\FileSystem\Filename;

class Executor {
    public function execute(Filename $commandFilename, string $argLine): ExecutorResult {
        $this->ensureFileExists($commandFilename);
        $this->ensureExecutable($commandFilename);

        $executable = sprintf(
            '%s %s',
            escapeshellarg($commandFilename->asString()),
            $argLine
        );
        exec($executable, $output, $rc);

        return new ExecutorResult(
            $executable,
            $output,
            $rc
        );
    }

    /**
     * @throws ExecutorException
     */
    private function ensureFileExists(Filename $executable): void {
        if (!$executable->exists()) {
            throw new ExecutorException(
                sprintf(
                    'Given executable "%s" does not exist',
                    $executable->asString()
                ),
                ExecutorException::NotFound
            );
        }
    }

    /**
     * @throws ExecutorException
     */
    private function ensureExecutable(Filename $executable): void {
        if (!$executable->isExecutable()) {
            throw new ExecutorException(
                sprintf(
                    'Given executable "%s" is not executable',
                    $executable->asString()
                ),
                ExecutorException::NotExecutable
            );
        }
    }
}
