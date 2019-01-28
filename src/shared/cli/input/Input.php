<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

interface Input {
    public function confirm(string $message, bool $default = true): bool;
}
