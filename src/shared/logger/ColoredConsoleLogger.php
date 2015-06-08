<?php
namespace TheSeer\Phive {

    class ColoredConsoleLogger extends ConsoleLogger {

        /**
         * @param string $errorMessage
         */
        public function logError($errorMessage) {
            $errorMessage = sprintf("\033[0;31m %s \033[0m", $errorMessage);
            parent::logError($errorMessage);
        }

        /**
         * @param string $warningMessage
         */
        public function logWarning($warningMessage) {
            $warningMessage = sprintf("\033[1;33m %s \033[0m", $warningMessage);
            parent::logWarning($warningMessage);
        }

    }

}
