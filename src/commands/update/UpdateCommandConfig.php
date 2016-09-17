<?php
namespace PharIo\Phive;

class UpdateCommandConfig {

    /**
     * @var Cli\Options
     */
    private $cliOptions;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @var TargetDirectoryLocator
     */
    private $targetDirectoryLocator;

    /**
     * @param Cli\Options            $cliOptions
     * @param PhiveXmlConfig         $phiveXmlConfig
     * @param TargetDirectoryLocator $targetDirectoryLocator
     */
    public function __construct(
        Cli\Options $cliOptions,
        PhiveXmlConfig $phiveXmlConfig,
        TargetDirectoryLocator $targetDirectoryLocator
    ) {
        $this->cliOptions = $cliOptions;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    /**
     * @return RequestedPhar[]
     */
    public function getRequestedPhars() {
        $filter = $this->getPharsFromCliArguments();
        return $this->getPharAliasesFromPhiveXmlConfig($filter);
    }

    /**
     * @return Directory
     */
    public function getTargetDirectory() {
        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @param array $filter
     *
     * @return RequestedPhar[]
     */
    private function getPharAliasesFromPhiveXmlConfig(array $filter) {
        $phars = [];
        foreach ($this->phiveXmlConfig->getPhars() as $configuredPhar) {
            if (!empty($filter) && !in_array((string)$configuredPhar->getName(), $filter)) {
                continue;
            }

            if (Url::isUrl($configuredPhar->getName())) {
                $identifier = new PharUrl($configuredPhar->getName());
            } elseif ($configuredPhar->hasUrl()) {
                $identifier = new PharUrl($configuredPhar->getUrl());
            } else {
                $identifier = new PharAlias($configuredPhar->getName());
            }

            $location = $configuredPhar->hasLocation() ? $configuredPhar->getLocation() : null;

            $phars[] = new RequestedPhar(
                $identifier,
                $configuredPhar->getVersionConstraint(),
                $configuredPhar->getVersionConstraint(),
                $location
            );
        }
        return $phars;
    }

    /**
     * @return string[]
     * @throws Cli\CommandOptionsException
     */
    private function getPharsFromCliArguments() {
        $phars = [];
        $argCount = $this->cliOptions->getArgumentCount();
        for ($i = 0; $i < $argCount; $i++) {
            $phars[] = $this->cliOptions->getArgument($i);
        }
        return $phars;
    }
}
