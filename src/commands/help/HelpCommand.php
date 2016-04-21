<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class HelpCommand implements Cli\Command {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param Environment $environment
     * @param Cli\Output  $output
     */
    public function __construct(Environment $environment, Cli\Output $output) {
        $this->environment = $environment;
        $this->output = $output;
    }

    public function execute() {
        $this->output->writeText(
            str_replace('%phive', $this->environment->getPhiveCommandPath(), file_get_contents(__DIR__ . '/help.txt'))
            . "\n\n"
        );
    }

}
