<?php

namespace PharIo\Phive;

class DefaultCommandConfig {
    /**
     * @var Cli\Options
     */
    private $cliOptions;

    public function __construct(Cli\Options $cliOptions) {
        $this->cliOptions = $cliOptions;
    }

    public function hasVersionOption() {
        return $this->cliOptions->hasOption('version');
    }
}
