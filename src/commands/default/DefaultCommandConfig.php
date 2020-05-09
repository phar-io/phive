<?php declare(strict_types = 1);
namespace PharIo\Phive;

class DefaultCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;

    public function __construct(Cli\Options $cliOptions) {
        $this->cliOptions = $cliOptions;
    }

    public function hasVersionOption(): bool {
        return $this->cliOptions->hasOption('version');
    }
}
