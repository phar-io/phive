<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ListCommand implements Cli\Command {

    /**
     * @var SourcesList
     */
    private $sourcesList;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param SourcesList $sourcesList
     * @param Cli\Output  $output
     */
    public function __construct(SourcesList $sourcesList, Cli\Output $output) {
        $this->sourcesList = $sourcesList;
        $this->output = $output;
    }

    /**
     *
     */
    public function execute() {
        $this->output->writeText("List of Aliases known to your system:\n");
        foreach ($this->sourcesList->getAliases() as $aliasName) {
            $this->output->writeText("* {$aliasName}\n");
        }
    }

}
