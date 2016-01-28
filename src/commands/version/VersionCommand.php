<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class VersionCommand implements Cli\Command {

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param PhiveVersion $version
     * @param Cli\Output   $output
     */
    public function __construct(PhiveVersion $version, Cli\Output $output) {
        $this->version = $version;
        $this->output = $output;
    }

    public function execute() {
        $this->output->writeText($this->version->getVersionString() . "\n\n");
    }

}
