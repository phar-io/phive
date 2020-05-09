<?php declare(strict_types = 1);
namespace PharIo\Phive;

class MigrateCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;

    public function __construct(Cli\Options $options) {
        $this->cliOptions = $options;
    }

    public function showStatus(): bool {
        return $this->cliOptions->hasOption('status');
    }
}
