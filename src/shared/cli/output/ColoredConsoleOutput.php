<?php
namespace PharIo\Phive\Cli;

class ColoredConsoleOutput extends ConsoleOutput {

    /**
     * @param string $errorMessage
     */
    public function writeError($errorMessage) {
        $errorMessage = sprintf("\033[0;31m %s \033[0m", $errorMessage);
        parent::writeError($errorMessage);
    }

    /**
     * @param string $warningMessage
     */
    public function writeWarning($warningMessage) {
        $warningMessage = sprintf("\033[1;33m %s \033[0m", $warningMessage);
        parent::writeWarning($warningMessage);
    }

    public function writeMarkdown($markdown) {

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        // bold => yellow
        $markdown = preg_replace_callback('/(\*\*|__)(.*?)\1/', function($matches) {
            return "\033[33m" . $matches[2] . "\033[0m"; // 0m
        }, $markdown);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        // italic => green
        $markdown = preg_replace_callback('/(\*|_)(.*?)\1/', function($matches) {
            return "\033[32m" . $matches[2] . "\033[0m";
        }, $markdown);

        $this->writeText($markdown);
    }

}
