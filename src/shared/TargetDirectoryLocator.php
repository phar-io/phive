<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\Phive\Cli\Options;

class TargetDirectoryLocator {
    /** @var Config */
    private $config;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /** @var Options */
    private $cliOptions;

    public function __construct(Config $config, PhiveXmlConfig $phiveXmlConfig, Options $cliOptions) {
        $this->config         = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->cliOptions     = $cliOptions;
    }

    /**
     * @throws Cli\CommandOptionsException
     * @throws ConfigException
     */
    public function getTargetDirectory(): Directory {

        switch (true) {
            case $this->cliOptions->hasOption('target'): {
                $directory = new Directory($this->cliOptions->getOption('target'));
                break;
            }

            case $this->phiveXmlConfig->hasTargetDirectory(): {
                $directory = $this->phiveXmlConfig->getTargetDirectory();
                break;
            }

            default: {
               $directory = $this->config->getToolsDirectory();
            }
        }

        $directory->ensureExists();
        return $directory;
    }
}
