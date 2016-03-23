<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class PurgeCommandConfig {

    /**
     * @var Cli\Options
     */
    private $cliOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * InstallCommandConfig constructor.
     *
     * @param Cli\Options $options
     * @param Config      $config
     */
    public function __construct(Cli\Options $options, Config $config) {
        $this->cliOptions = $options;
        $this->config = $config;
    }

}
