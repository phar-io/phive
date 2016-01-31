<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ListCommand implements Cli\Command {

    /**
     * @var PharIoRepositoryList
     */
    private $pharIoRepositoryList;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param PharIoRepositoryList $pharIoRepositoryList
     * @param Cli\Output   $output
     */
    public function __construct(
        PharIoRepositoryList $pharIoRepositoryList,
        Cli\Output $output
    ) {
        $this->pharIoRepositoryList = $pharIoRepositoryList;
        $this->output = $output;
    }

    /**
     *
     */
    public function execute() {
        $this->output->writeText("List of Aliases known to your system:\n");
        foreach ($this->pharIoRepositoryList->getAliases() as $aliasName) {
            $this->output->writeText("* {$aliasName}\n");
        }
    }

}
