<?php
namespace PharIo\Phive;

class ExecutorResult {

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $output;

    /**
     * @var int
     */
    private $exitCode;

    /**
     * ExecutorResult constructor.
     *
     * @param string   $command
     * @param string[] $output
     * @param int      $exitCode
     */
    public function __construct($command, array $output, $exitCode) {
        $this->command = $command;
        $this->output = $output;
        $this->exitCode = $exitCode;
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->exitCode === 0;
    }

    /**
     * @return int
     */
    public function getExitCode() {
        return $this->exitCode;
    }

    /**
     * @return string[]
     */
    public function getOutput() {
        return $this->output;
    }
}
