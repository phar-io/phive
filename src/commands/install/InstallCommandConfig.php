<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\Phive\Cli;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PharIo\Version\VersionConstraint;
use PharIo\Version\VersionConstraintParser;

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
     * @var Environment
     */
    private $environment;

    /**
     * @var TargetDirectoryLocator
     */
    private $targetDirectoryLocator;

    /**
     * @param Cli\Options            $options
     * @param PhiveXmlConfig         $phiveXmlConfig
     * @param Environment            $environment
     * @param TargetDirectoryLocator $targetDirectoryLocator
     */
    public function __construct(
        Cli\Options $options,
        PhiveXmlConfig $phiveXmlConfig,
        Environment $environment,
        TargetDirectoryLocator $targetDirectoryLocator
    ) {
        $this->cliOptions = $options;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->environment = $environment;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    /**
     * @return Directory
     * @throws \PharIo\Phive\ConfigException
     * @throws \PharIo\Phive\Cli\CommandOptionsException
     */
    public function getTargetDirectory() {
        if ($this->installGlobally()) {
            return $this->environment->getGlobalBinDir();
        }

        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @return RequestedPhar[]
     * @throws \PharIo\Phive\UnsupportedVersionConstraintException
     * @throws \PharIo\Phive\InstallCommandConfigException
     * @throws \PharIo\Phive\ConfiguredPharException
     * @throws Cli\CommandOptionsException
     */
    public function getRequestedPhars() {
        if ($this->cliOptions->getArgumentCount() === 0) {
            return $this->getPharsFromPhiveXmlConfig();
        }

        return $this->getPharsFromCliArguments();
    }

    /**
     * @return RequestedPhar[]
     * @throws \PharIo\Phive\ConfiguredPharException
     */
    private function getPharsFromPhiveXmlConfig() {
        $phars = [];
        foreach ($this->phiveXmlConfig->getPhars() as $configuredPhar) {
            $location = $configuredPhar->hasLocation() ? $configuredPhar->getLocation() : null;

            $phars[] = new RequestedPhar(
                $this->getIdentifier($configuredPhar),
                $configuredPhar->getVersionConstraint(),
                $this->getVersionToInstall($configuredPhar),
                $location,
                $configuredPhar->isCopy()
            );
        }

        return $phars;
    }

    /**
     * @return RequestedPhar[]
     * @throws \PharIo\Phive\InstallCommandConfigException
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
                        'Cannot install from non HTTPS URL', InstallCommandConfigException::UnsupportedProtocol
                    );
                }
                $identifier = new PharUrl($argument);
                $versionConstraint = new ExactVersionConstraint(
                    $identifier->getPharVersion()->getVersionString()
                );
            } else {
                $argumentParts = preg_split('/[@:=]/', $argument, 2, PREG_SPLIT_NO_EMPTY);
                $identifier = new PharAlias(mb_strtolower($argumentParts[0]));
                if (count($argumentParts) === 2) {
                    $versionConstraint = (new VersionConstraintParser())->parse($argumentParts[1]);
                } else {
                    $versionConstraint = new AnyVersionConstraint();
                }
            }

            $phars[] = new RequestedPhar(
                $identifier, $versionConstraint, $versionConstraint, null, $this->makeCopy()
            );
        }

        return $phars;
    }

    /**
     * @return bool
     */
    private function installGlobally() {
        return $this->cliOptions->hasOption('global');
    }

    /**
     * @return bool
     */
    private function makeCopy() {
        return $this->cliOptions->hasOption('copy') || $this->installGlobally();
    }

    /**
     * @return bool
     */
    public function doNotAddToPhiveXml() {
        return $this->cliOptions->hasOption('temporary') || $this->installGlobally();
    }

    /**
     * @return bool
     */
    public function forceAcceptUnsignedPhars() {
        return $this->cliOptions->hasOption('force-accept-unsigned');
    }

    /**
     * @param ConfiguredPhar $configuredPhar
     *
     * @return PharAlias|PharUrl
     * @throws \PharIo\Phive\ConfiguredPharException
     */
    private function getIdentifier(ConfiguredPhar $configuredPhar) {
        if (Url::isUrl($configuredPhar->getName())) {
            return new PharUrl($configuredPhar->getName());
        }

        if ($configuredPhar->hasUrl()) {
            return $configuredPhar->getUrl();
        }

        return new PharAlias($configuredPhar->getName());
    }

    /**
     * @param ConfiguredPhar $configuredPhar
     *
     * @return VersionConstraint
     * @throws \PharIo\Phive\ConfiguredPharException
     */
    private function getVersionToInstall(ConfiguredPhar $configuredPhar) {
        $versionConstraint = $configuredPhar->getVersionConstraint();
        if ($configuredPhar->isInstalled() && $versionConstraint->complies($configuredPhar->getInstalledVersion())) {
            return new ExactVersionConstraint($configuredPhar->getInstalledVersion()->getVersionString());
        }

        return $versionConstraint;
    }

}
