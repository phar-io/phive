<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class Executor {
    public function execute(Filename $commandFilename, string $argLine): ExecutorResult {
        $this->ensureFileExists($commandFilename);
        $this->ensureExecutable($commandFilename);

        $executable = \sprintf(
            '%s %s',
            \escapeshellarg($commandFilename->asString()),
            $argLine
        );
        \exec($executable, $output, $rc);

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
                \sprintf(
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
                \sprintf(
                    'Given executable "%s" is not executable',
                    $executable->asString()
                ),
                ExecutorException::NotExecutable
            );
        }
    }
}
