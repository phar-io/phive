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

}
