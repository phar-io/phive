<?php
namespace PharIo\Phive\Cli;

interface Output {

    /**
     * @param string $textMessage
     */
    public function writeText($textMessage);

    /**
     * @param string $infoMessage
     */
    public function writeInfo($infoMessage);

    /**
     * @param string $warningMessage
     */
    public function writeWarning($warningMessage);

    /**
     * @param string $errorMessage
     */
    public function writeError($errorMessage);

}
