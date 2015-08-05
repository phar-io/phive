<?php
namespace PharIo\Phive {

    class ConsoleLogger implements Logger {

        const VERBOSE_ERROR = 1;
        const VERBOSE_WARNING = 2;
        const VERBOSE_INFO = 3;

        /**
         * @var int
         */
        private $verbosity = self::VERBOSE_INFO;

        /**
         * @param int $verbosity
         */
        public function __construct($verbosity) {
            $this->setVerbosity($verbosity);
        }

        /**
         * @param int $verbosity
         */
        private function setVerbosity($verbosity) {
            if (!in_array($verbosity, [self::VERBOSE_ERROR, self::VERBOSE_INFO, self::VERBOSE_WARNING])) {
                throw new \InvalidArgumentException('Invalid value for verbosity');
            }
            $this->verbosity = $verbosity;
        }

        /**
         * @param string $infoMessage
         */
        public function logInfo($infoMessage) {
            if ($this->verbosity >= self::VERBOSE_INFO) {
                fwrite(STDOUT, $infoMessage . "\n");
            }
        }

        /**
         * @param string $errorMessage
         */
        public function logError($errorMessage) {
            if ($this->verbosity >= self::VERBOSE_ERROR) {
                fwrite(STDERR, '[ERROR]   ' . $errorMessage . "\n");
            }
        }

        /**
         * @param string $warningMessage
         */
        public function logWarning($warningMessage) {
            if ($this->verbosity >= self::VERBOSE_WARNING) {
                fwrite(STDOUT, '[WARNING] ' . $warningMessage . "\n");
            }
        }

    }

}
