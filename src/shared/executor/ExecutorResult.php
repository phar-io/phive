<?php
namespace PharIo\Phive;

class ExecutorResult {

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $output;

    /**
     * @var int
     */
    private $exitCode;

    /**
     * ExecutorResult constructor.
     *
     * @param string $command
     * @param string $output
     * @param int    $exitCode
     */
    public function __construct($command, $output, $exitCode) {
        $this->command = $command;
        $this->output = $output;
        $this->exitCode = $exitCode;
    }

    public function isSuccess() {
        return $this->exitCode === 0;
    }

    public function getExitCode() {
        return $this->exitCode;
    }

    public function getOutput() {
        return $this->output;
    }
}
