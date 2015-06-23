<?php
namespace TheSeer\Phive {

    interface Logger {

        /**
         * @param string $infoMessage
         */
        public function logInfo($infoMessage);

        /**
         * @param string $errorMessage
         */
        public function logError($errorMessage);

        /**
         * @param string $warningMessage
         */
        public function logWarning($warningMessage);

    }

}

