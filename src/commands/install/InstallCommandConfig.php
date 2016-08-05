<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class InstallCommandConfig {

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
     * @param Cli\Options            $options
     * @param PhiveXmlConfig         $phiveXmlConfig
     * @param TargetDirectoryLocator $targetDirectoryLocator
     */
    public function __construct(Cli\Options $options, PhiveXmlConfig $phiveXmlConfig, TargetDirectoryLocator $targetDirectoryLocator) {
        $this->cliOptions = $options;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    /**
     * @return Directory
     */
    public function getTargetDirectory() {
        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @return RequestedPhar[]
     * @throws Cli\CommandOptionsException
     */
    public function getRequestedPhars() {
        if ($this->cliOptions->getArgumentCount() == 0) {
            return $this->getPharsFromPhiveXmlConfig();
        }
        return $this->getPharsFromCliArguments();
    }

    /**
     * @return RequestedPhar[]
     */
    private function getPharsFromPhiveXmlConfig() {
        $phars = [];
        foreach ($this->phiveXmlConfig->getPhars() as $configuredPhar) {
            if (Url::isUrl($configuredPhar->getName())) {
                $phars[] = new RequestedPharUrl(new PharUrl($configuredPhar->getName()));
            } else {
                $phars[] = $this->getPharAliasFromConfiguredPhar($configuredPhar);
            }
        }

        return $phars;
    }

    /**
     * @param ConfiguredPhar $configuredPhar
     *
     * @return RequestedPharAlias
     */
    private function getPharAliasFromConfiguredPhar(ConfiguredPhar $configuredPhar) {
        $versionConstraint = $configuredPhar->getVersionConstraint();
        $versionToInstall = null;
        if ($configuredPhar->isInstalled()) {
            $versionToInstall = new ExactVersionConstraint($configuredPhar->getInstalledVersion()->getVersionString());
        } else {
            $versionToInstall = $versionConstraint;
        }
        return new RequestedPharAlias(
            new PharAlias(
                $configuredPhar->getName(),
                $versionConstraint,
                $versionToInstall
            )
        );
    }

    /**
     * @return RequestedPhar[]
     * @throws Cli\CommandOptionsException
     * @throws UnsupportedVersionConstraintException
     */
    private function getPharsFromCliArguments() {
        $phars = [];
        $argCount = $this->cliOptions->getArgumentCount();
        for ($i = 0; $i < $argCount; $i++) {
            $argument = $this->cliOptions->getArgument($i);
            if (Url::isUrl($argument)) {
                $phars[] = new RequestedPharUrl(new PharUrl($argument));
            } else {
                $aliasSegments = preg_split('/[@:=]/', $argument, 2, PREG_SPLIT_NO_EMPTY);
                if (count($aliasSegments) === 2) {
                    $versionConstraint = (new VersionConstraintParser())->parse($aliasSegments[1]);
                } else {
                    $versionConstraint = new AnyVersionConstraint();
                }
                $phars[] = new RequestedPharAlias(new PharAlias($aliasSegments[0], $versionConstraint, $versionConstraint));
            }
        }
        return $phars;
    }

    /**
     * @return bool
     */
    public function installGlobally() {
        return $this->cliOptions->hasOption('global');
    }

    /**
     * @return bool
     */
    public function makeCopy() {
        return $this->cliOptions->hasOption('copy') || $this->installGlobally();
    }

    /**
     * @return bool
     */
    public function doNotAddToPhiveXml() {
        return $this->cliOptions->hasOption('temporary') || $this->installGlobally();
    }

}
