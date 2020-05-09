<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ResetCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;

    public function __construct(Cli\Options $cliOptions) {
        $this->cliOptions = $cliOptions;
    }

    public function hasAliases(): bool {
        return $this->cliOptions->getArgumentCount() > 0;
    }

    /**
     * @throws Cli\CommandOptionsException
     */
    public function getAliases(): array {
        return $this->cliOptions->getArguments();
    }
}
