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
        if ($this->cliOptions->hasOption('target')) {
            $path = $this->cliOptions->getOption('target');

            if ($path[0] === '/') {
                return new Directory($path);
            }

            return $this->config->getWorkingDirectory()->child($path);
        }

        if ($this->phiveXmlConfig->hasTargetDirectory()) {
            return $this->phiveXmlConfig->getTargetDirectory();
        }

        return $this->config->getToolsDirectory();
    }
}
