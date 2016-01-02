<?php
namespace PharIo\Phive;

use TheSeer\CLI\Command;

class VersionCommand implements Command {

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * @var Output
     */
    private $output;

    /**
     * @param PhiveVersion $version
     * @param Output       $output
     */
    public function __construct(PhiveVersion $version, Output $output) {
        $this->version = $version;
        $this->output = $output;
    }

    public function execute() {
        $this->output->writeText($this->version->getVersionString() . "\n\n");
    }

}


