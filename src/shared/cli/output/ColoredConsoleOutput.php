<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

class ColoredConsoleOutput extends ConsoleOutput {
    public function writeError(string $errorMessage): void {
        $errorMessage = \sprintf("\033[0;31m %s \033[0m", $errorMessage);
        parent::writeError($errorMessage);
    }

    public function writeWarning(string $warningMessage): void {
        $warningMessage = \sprintf("\033[1;33m %s \033[0m", $warningMessage);
        parent::writeWarning($warningMessage);
    }

    public function writeMarkdown(string $markdown): void {

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        // bold => yellow
        $markdown = \preg_replace_callback('/(\*\*|__)(.*?)\1/', function ($matches) {
            return "\033[33m" . $matches[2] . "\033[0m"; // 0m
        }, $markdown);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        // italic => green
        $markdown = \preg_replace_callback('/(\*|_)(.*?)\1/', function ($matches) {
            return "\033[32m" . $matches[2] . "\033[0m";
        }, $markdown);

        $this->writeText($markdown);
    }
}
