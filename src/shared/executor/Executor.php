<?php
namespace PharIo\Phive;

class Executor {
    /**
     * @param Filename $commandFilename
     * @param string $argLine
     *
     * @return ExecutorResult
     */
    public function execute(Filename $commandFilename, $argLine) {
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

    private function ensureFileExists(Filename $executable) {
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

    private function ensureExecutable(Filename $executable) {
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
