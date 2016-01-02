<?php
namespace PharIo\Phive;

use TheSeer\CLI\Command;

class HelpCommand implements Command {

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var Output
     */
    private $output;

    /**
     * @param PhiveVersion $version
     * @param Environment  $environment
     * @param Output       $output
     */
    public function __construct(PhiveVersion $version, Environment $environment, Output $output) {
        $this->version = $version;
        $this->environment = $environment;
        $this->output = $output;
    }

    public function execute() {
        $this->output->writeText($this->version->getVersionString() . "\n\n");
        $this->output->writeText(
            str_replace('%phive', $this->environment->getBinaryName(), file_get_contents(__DIR__ . '/help.txt'))
            . "\n\n"
        );
    }

}
