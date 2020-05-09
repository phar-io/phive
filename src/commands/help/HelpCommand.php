<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HelpCommand implements Cli\Command {

    /** @var Environment */
    private $environment;

    /** @var Cli\Output */
    private $output;

    public function __construct(Environment $environment, Cli\Output $output) {
        $this->environment = $environment;
        $this->output      = $output;
    }

    public function execute(): void {
        $this->output->writeMarkdown(
            \str_replace(
                '%phive',
                $this->environment->getPhiveCommandPath(),
                \file_get_contents(__DIR__ . '/help.md')
            ) . "\n\n"
        );
    }
}
