<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ListCommand implements Cli\Command {

    /** @var SourcesList */
    private $sourcesList;

    /** @var SourcesList */
    private $localSources;

    /** @var Cli\Output */
    private $output;

    public function __construct(SourcesList $sourcesList, SourcesList $localSources, Cli\Output $output) {
        $this->sourcesList  = $sourcesList;
        $this->localSources = $localSources;
        $this->output       = $output;
    }

    public function execute(): void {
        $localAliases = $this->localSources->getAliases();

        if (\count($localAliases) > 0) {
            $this->output->writeText("\nList of local aliases known to your system:\n");
            $this->printAliases($localAliases);
        }

        $this->output->writeText("\nList of phar.io resolved aliases known to your system:\n");
        $this->printAliases($this->sourcesList->getAliases());
    }

    private function printAliases(array $aliases): void {
        foreach ($aliases as $aliasName) {
            $this->output->writeText("* {$aliasName}\n");
        }
    }
}
