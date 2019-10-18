<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

interface Context {
    public function canContinue(): bool;

    public function knowsOption(string $option): bool;

    public function requiresValue(string $option): bool;

    public function getOptionForChar(string $char): string;

    public function hasOptionForChar(string $char): bool;

    public function acceptsArguments(): bool;

    public function addArgument(string $arg): void;

    /**
     * @param mixed $value
     */
    public function setOption(string $option, $value): void;

    public function getOptions(): Options;
}
