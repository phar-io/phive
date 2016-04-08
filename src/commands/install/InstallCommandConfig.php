<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class InstallCommandConfig {

    /**
     * @var Cli\Options
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
     * @param Cli\Options    $options
     * @param Config         $config
     * @param PhiveXmlConfig $phiveXmlConfig
     */
    public function __construct(Cli\Options $options, Config $config, PhiveXmlConfig $phiveXmlConfig) {
        $this->cliOptions = $options;
        $this->config = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    /**
     * @return Directory
     */
    public function getTargetDirectory() {
        return $this->config->getWorkingDirectory()->child('tools');
    }

    /**
     * @return RequestedPhar[]
     * @throws Cli\CommandOptionsException
     */
    public function getRequestedPhars() {
        if ($this->cliOptions->getArgumentCount() == 0) {
            return $this->phiveXmlConfig->getPhars();
        }
        return $this->getPharsFromCliArguments();
    }

    /**
     * @return RequestedPhar[]
     * @throws Cli\CommandOptionsException
     */
    private function getPharsFromCliArguments() {
        $phars = [];
        $argCount = $this->cliOptions->getArgumentCount();
        for ($i = 0; $i < $argCount; $i++) {
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
    public function installGlobally() {
        return $this->cliOptions->isSwitch('global');
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
    public function doNotAddToPhiveXml() {
        return $this->cliOptions->isSwitch('temporary');
    }

}
