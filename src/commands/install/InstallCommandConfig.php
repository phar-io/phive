<?php
namespace PharIo\Phive;

use TheSeer\CLI;

class InstallCommandConfig {

    /**
     * @var CLI\CommandOptions
     */
    private $cliOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @param CLI\CommandOptions $options
     * @param Config             $config
     * @param PhiveXmlConfig     $phiveXmlConfig
     */
    public function __construct(CLI\CommandOptions $options, Config $config, PhiveXmlConfig $phiveXmlConfig) {
        $this->cliOptions = $options;
        $this->config = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return $this->config->getWorkingDirectory();
    }

    /**
     * @return RequestedPhar[]
     * @throws CLI\CommandOptionsException
     */
    public function getRequestedPhars() {
        if ($this->cliOptions->getArgumentCount() == 0) {
            return $this->phiveXmlConfig->getPhars();
        }
        return $this->getPharsFromCliArguments();
    }

    /**
     * @return RequestedPhar[]
     * @throws CLI\CommandOptionsException
     */
    private function getPharsFromCliArguments() {
        $phars = [];
        for ($i = 0; $i < $this->cliOptions->getArgumentCount(); $i++) {
            $argument = $this->cliOptions->getArgument($i);
            if (strpos($argument, 'https://') !== false) {
                $phars[] = RequestedPhar::fromUrl(new Url($argument));
            } else {
                $aliasSegments = explode('@', $argument, 2);
                $parser = new VersionConstraintParser();
                if (count($aliasSegments) === 2) {
                    $versionConstraint = $parser->parse($aliasSegments[1]);
                } else {
                    $versionConstraint = new AnyVersionConstraint();
                }
                $phars[] = RequestedPhar::fromAlias(new PharAlias($aliasSegments[0], $versionConstraint));
            }
        }
        return $phars;
    }

    /**
     * @return bool
     */
    public function makeCopy() {
        return $this->cliOptions->isSwitch('copy');
    }

    /**
     * @return bool
     */
    public function saveToPhiveXml() {
        return $this->cliOptions->isSwitch('save');
    }

}


