<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HelpCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;
    private $helpList = [];

    public function __construct(Cli\Options $cliOptions) {
        $this->cliOptions = $cliOptions;
        $this->createHelpList();
    }

    public function generic(): bool {
        if ($this->cliOptions->hasOption('help')) {
            return false;
        }

        return $this->cliOptions->getArgumentCount() < 1;
    }

    /**
     * @throws Cli\CommandOptionsException
     *
     * @return string[]
     */
    public function getCommandsName(): array {
        if ($this->generic()) {
            return [];
        }
        $commands      = $this->cliOptions->getArguments();

        if ($this->cliOptions->hasOption('help')) {
            $commands[] = $this->cliOptions->getOption('help');
        }

        return $commands;
    }

    public function hasHelp(string $command): bool {
        return \array_key_exists($command, $this->helpList);
    }

    public function getHelpUsage(string $command): string {
        if (!$this->hasHelp($command)) {
            throw new \InvalidArgumentException();
        }

        return $this->helpList[$command]['usage'];
    }
    public function getHelpText(string $command): string {
        if (!$this->hasHelp($command)) {
            throw new \InvalidArgumentException();
        }

        return $this->helpList[$command]['help'];
    }

    private function createHelpList(): void {
        $help = \file_get_contents(__DIR__ . '/help.md');

        // Extract command help
        $helpStartAt = \strpos($help, '**Commands:**');
        $helpText    = \substr($help, $helpStartAt);
        \preg_match_all('/^\*\*(?P<command>[\w-]+)(?P<options_arguments> .+)?\*\*$/m', $helpText, $matches, \PREG_SET_ORDER);

        $helps = [];

        for ($index = 0, $indexMax = \count($matches); $index < $indexMax; $index++) {
            $match                    = $matches[$index];
            $startAt                  = \strpos($help, $match[0]);
            $endAt                    = $index === \count($matches) - 1 ? \strlen($help) : \strpos($help, $matches[$index + 1][0]);
            $helps[$match['command']] = [
                'usage' => $match['command'] . ($match['options_arguments'] ?? ''),
                'help'  => \substr($help, $startAt, $endAt - $startAt)
            ];
        }

        $this->helpList = $helps;
    }
}
