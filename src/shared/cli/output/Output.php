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

    /**
     * @param string $progressMessage
     */
    public function writeProgress($progressMessage);

    /**
     * @param string $markdown
     */
    public function writeMarkdown($markdown);

}
