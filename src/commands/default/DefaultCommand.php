<?php

namespace PharIo\Phive;

class DefaultCommand implements Cli\Command {

    /**
     * @var VersionCommand
     */
    private $versionCommand;

    /**
     * @var HelpCommand
     */
    private $helpCommand;

    /**
     * @var DefaultCommandConfig
     */
    private $config;

    public function __construct(
        VersionCommand $versionCommand,
        HelpCommand $helpCommand,
        DefaultCommandConfig $config

    ) {
        $this->versionCommand = $versionCommand;
        $this->helpCommand = $helpCommand;
        $this->config = $config;
    }

    public function execute() {
        if ($this->config->hasVersionOption()) {
            $this->versionCommand->execute();
            return;
        }

        $this->helpCommand->execute();
    }

}
