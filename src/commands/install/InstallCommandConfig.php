<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PharIo\Version\VersionConstraint;
use PharIo\Version\VersionConstraintParser;

class InstallCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /** @var Environment */
    private $environment;

    /** @var TargetDirectoryLocator */
    private $targetDirectoryLocator;

    public function __construct(
        Cli\Options $options,
        PhiveXmlConfig $phiveXmlConfig,
        Environment $environment,
        TargetDirectoryLocator $targetDirectoryLocator
    ) {
        $this->cliOptions             = $options;
        $this->phiveXmlConfig         = $phiveXmlConfig;
        $this->environment            = $environment;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    /**
     * @throws \PharIo\Phive\ConfigException
     * @throws \PharIo\Phive\Cli\CommandOptionsException
     */
    public function getTargetDirectory(): Directory {
        if ($this->installGlobally()) {
            return $this->environment->getGlobalBinDir();
        }

        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @throws \PharIo\Phive\UnsupportedVersionConstraintException
     * @throws \PharIo\Phive\InstallCommandConfigException
     * @throws \PharIo\Phive\ConfiguredPharException
     * @throws Cli\CommandOptionsException
     *
     * @return RequestedPhar[]
     */
    public function getRequestedPhars(): array {
        if ($this->cliOptions->getArgumentCount() === 0) {
            return $this->getPharsFromPhiveXmlConfig();
        }

        return $this->getPharsFromCliArguments();
    }

    public function doNotAddToPhiveXml(): bool {
        return $this->cliOptions->hasOption('temporary') || $this->installGlobally();
    }

    public function forceAcceptUnsignedPhars(): bool {
        return $this->cliOptions->hasOption('force-accept-unsigned');
    }

    /**
     * @throws \PharIo\Phive\ConfiguredPharException
     *
     * @return RequestedPhar[]
     */
    private function getPharsFromPhiveXmlConfig(): array {
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
     * @throws \PharIo\Phive\InstallCommandConfigException
     * @throws Cli\CommandOptionsException
     * @throws UnsupportedVersionConstraintException
     *
     * @return RequestedPhar[]
     */
    private function getPharsFromCliArguments(): array {
        $phars    = [];
        $argCount = $this->cliOptions->getArgumentCount();

        for ($i = 0; $i < $argCount; $i++) {
            $argument = $this->cliOptions->getArgument($i);

            if (Url::isUrl($argument)) {
                if (!Url::isHttpsUrl($argument)) {
                    throw new InstallCommandConfigException(
                        'Cannot install from non HTTPS URL',
                        InstallCommandConfigException::UnsupportedProtocol
                    );
                }
                $identifier        = new PharUrl($argument);
                $versionConstraint = new ExactVersionConstraint(
                    $identifier->getPharVersion()->getVersionString()
                );
            } else {
                $argumentParts = \preg_split('/[@:=]/', $argument, 2, \PREG_SPLIT_NO_EMPTY);
                $identifier    = new PharAlias(\mb_strtolower($argumentParts[0]));

                if (\count($argumentParts) === 2) {
                    $versionConstraint = (new VersionConstraintParser())->parse($argumentParts[1]);
                } else {
                    $versionConstraint = new AnyVersionConstraint();
                }
            }

            $phars[] = new RequestedPhar(
                $identifier,
                $versionConstraint,
                $versionConstraint,
                null,
                $this->makeCopy()
            );
        }

        return $phars;
    }

    private function installGlobally(): bool {
        return $this->cliOptions->hasOption('global');
    }

    private function makeCopy(): bool {
        return $this->cliOptions->hasOption('copy') || $this->installGlobally();
    }

    /**
     * @throws \PharIo\Phive\ConfiguredPharException
     */
    private function getIdentifier(ConfiguredPhar $configuredPhar): PharIdentifier {
        if (Url::isUrl($configuredPhar->getName())) {
            return new PharUrl($configuredPhar->getName());
        }

        if ($configuredPhar->hasUrl()) {
            return $configuredPhar->getUrl();
        }

        return new PharAlias($configuredPhar->getName());
    }

    /**
     * @throws \PharIo\Phive\ConfiguredPharException
     */
    private function getVersionToInstall(ConfiguredPhar $configuredPhar): VersionConstraint {
        $versionConstraint = $configuredPhar->getVersionConstraint();

        if ($configuredPhar->isInstalled() && $versionConstraint->complies($configuredPhar->getInstalledVersion())) {
            return new ExactVersionConstraint($configuredPhar->getInstalledVersion()->getVersionString());
        }

        return $versionConstraint;
    }
}
