<?php
namespace PharIo\Phive;

class Executor {

    /**
     * @var Filename
     */
    private $executable;

    /**
     * Executor constructor.
     *
     * @param Filename $executable
     */
    public function __construct(Filename $executable) {
        $this->ensureFileExists($executable);
        $this->ensureExecutable($executable);
        $this->executable = $executable;
    }

    /**
     * @param string $argLine
     *
     * @return ExecutorResult
     */
    public function execute($argLine) {
        $command = sprintf(
            '%s %s',
            escapeshellarg($this->executable->asString()),
            $argLine
        );
        exec($command, $output, $rc);

        return new ExecutorResult(
            $command,
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
