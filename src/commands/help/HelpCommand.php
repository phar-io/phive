<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HelpCommand implements Cli\Command {

    /** @var Environment */
    private $environment;

    /** @var Cli\Output */
    private $output;

    /** @var HelpCommandConfig */
    private $config;

    public function __construct(Environment $environment, Cli\Output $output, HelpCommandConfig $config) {
        $this->environment = $environment;
        $this->output      = $output;
        $this->config      = $config;
    }

    public function execute(): void {
        if ($this->config->generic()) {
            $this->output->writeMarkdown(
                \str_replace(
                    '%phive',
                    $this->environment->getPhiveCommandPath(),
                    \file_get_contents(__DIR__ . '/help.md')
                ) . "\n\n"
            );

            return;
        }

        $commands = \array_reduce($this->config->getCommandsName(), function (array $statues, string $command) {
            if ($this->config->hasHelp($command)) {
                $statues['found'][] = $command;
            } else {
                $statues['not-found'][] = $command;
            }

            return $statues;
        }, ['found' => [], 'not-found' => []]);

        foreach ($commands['not-found'] as $command) {
            $this->output->writeWarning(\sprintf('No help found for command "%s"', $command));
        }

        $usages = [];
        $help   = [];

        foreach ($commands['found'] as $command) {
            $usages[] = $this->getHelpPrefix($this->config->getHelpUsage($command));
            $help[]   = $this->config->getHelpText($command);
        }

        $this->output->writeMarkdown(\sprintf(
            \PHP_EOL . '%s' . \PHP_EOL . \PHP_EOL . '%s' . \PHP_EOL . \PHP_EOL . '%s',
            \implode(\PHP_EOL, $usages),
            $this->getGlobalOptions(),
            \implode(\PHP_EOL, $help)
        ));
    }

    private function getHelpPrefix(string $command): string {
        return \sprintf('**Usage**: %s [global-options] %s', $this->environment->getPhiveCommandPath(), $command);
    }

    private function getGlobalOptions(): string {
        $help = \file_get_contents(__DIR__ . '/help.md');
        \preg_match('/^\*\*Global options:\*\*$[^*]+$/m', $help, $match);

        return $match[0] ?? '';
    }
}
