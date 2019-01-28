<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

interface Output {
    public function writeText(string $textMessage): void;
    public function writeInfo(string $infoMessage): void;
    public function writeWarning(string $warningMessage): void;
    public function writeError(string $errorMessage): void;
    public function writeProgress(string $progressMessage): void;
    public function writeMarkdown(string $markdown): void;
}
