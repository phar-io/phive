<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ExecutorResult {

    /** @var string */
    private $command;

    /** @var array */
    private $output;

    /** @var int */
    private $exitCode;

    public function __construct(string $command, array $output, int $exitCode) {
        $this->command  = $command;
        $this->output   = $output;
        $this->exitCode = $exitCode;
    }

    public function isSuccess(): bool {
        return $this->exitCode === 0;
    }

    public function getExitCode(): int {
        return $this->exitCode;
    }

    /**
     * @return string[]
     */
    public function getOutput(): array {
        return $this->output;
    }

    public function getCommand(): string {
        return $this->command;
    }
}
