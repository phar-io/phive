<?php
namespace PharIo\Phive\Cli;

class ConsoleOutput implements Output {

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
    public function writeInfo($infoMessage) {
        if ($this->verbosity >= self::VERBOSE_INFO) {
            $this->writeText($infoMessage . "\n");
        }
    }

    /**
     * @param $textMessage
     */
    public function writeText($textMessage) {
        fwrite(STDOUT, $textMessage);
    }

    /**
     * @param string $warningMessage
     */
    public function writeWarning($warningMessage) {
        if ($this->verbosity >= self::VERBOSE_WARNING) {
            $this->writeText('[WARNING] ' . $warningMessage . "\n");
        }
    }

    /**
     * @param string $errorMessage
     */
    public function writeError($errorMessage) {
        if ($this->verbosity >= self::VERBOSE_ERROR) {
            fwrite(STDERR, '[ERROR]   ' . $errorMessage . "\n");
        }
    }

}

