<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ListCommand implements Cli\Command {

    /**
     * @var PharRepositoryList
     */
    private $pharRepositoryList;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param PharRepositoryList $pharRepositoryList
     * @param Cli\Output $output
     */
    public function __construct(
        PharRepositoryList $pharRepositoryList,
        Cli\Output $output
    ) {
        $this->pharRepositoryList = $pharRepositoryList;
        $this->output = $output;
    }

    /**
     *
     */
    public function execute() {
        $this->output->writeText("List of Aliases known to your system:\n");
        foreach ($this->pharRepositoryList->getAliases() as $aliasName) {
            $this->output->writeText("* {$aliasName}\n");
        }
    }

}
