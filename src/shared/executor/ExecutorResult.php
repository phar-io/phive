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
