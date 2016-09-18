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
                $identifier = new PharUrl($configuredPhar->getName());
            } elseif ($configuredPhar->hasUrl()) {
                $identifier = new PharUrl($configuredPhar->getUrl());
            } else {
                $identifier = new PharAlias($configuredPhar->getName());
            }

            $versionConstraint = $configuredPhar->getVersionConstraint();
            if ($configuredPhar->isInstalled() &&
                $configuredPhar->getVersionConstraint()->complies($configuredPhar->getInstalledVersion())
            ) {
                $versionToInstall = new ExactVersionConstraint(
                    $configuredPhar->getInstalledVersion()->getVersionString()
                );
            } else {
                $versionToInstall = $versionConstraint;
            }

            $location = $configuredPhar->hasLocation() ? $configuredPhar->getLocation() : null;

            $phars[] = new RequestedPhar(
                $identifier,
                $configuredPhar->getVersionConstraint(),
                $versionToInstall,
                $location
            );
        }

        return $phars;
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
                if (!Url::isHttpsUrl($argument)) {
                    throw new InstallCommandConfigException(
                        "Cannot install from non HTTPS URL",
                        InstallCommandConfigException::UnsupportedProtocol
                    );
                }
                $identifier = new PharUrl($argument);
                $versionConstraint = new ExactVersionConstraint(
                    $identifier->getPharVersion()->getVersionString()
                );
            } else {
                $argumentParts = preg_split('/[@:=]/', $argument, 2, PREG_SPLIT_NO_EMPTY);
                $identifier = new PharAlias($argumentParts[0]);
                if (count($argumentParts) === 2) {
                    $versionConstraint = (new VersionConstraintParser())->parse($argumentParts[1]);
                } else {
                    $versionConstraint = new AnyVersionConstraint();
                }
            }

            $phars[] = new RequestedPhar($identifier, $versionConstraint, $versionConstraint);
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
