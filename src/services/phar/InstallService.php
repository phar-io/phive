<?php
namespace PharIo\Phive;

use PharIo\Version\AndVersionConstraintGroup;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\GreaterThanOrEqualToVersionConstraint;
use PharIo\Version\SpecificMajorVersionConstraint;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;

class InstallService {

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXml;

    /**
     * @var PharInstaller
     */
    private $installer;

    /**
     * @var PharRegistry
     */
    private $registry;

    /**
     * @var PharService
     */
    private $pharService;

    /**
     * @param PhiveXmlConfig $phiveXml
     * @param PharInstaller $installer
     * @param PharRegistry $registry
     * @param PharService $pharService
     */
    public function __construct(
        PhiveXmlConfig $phiveXml,
        PharInstaller $installer,
        PharRegistry $registry,
        PharService $pharService
    ) {
        $this->phiveXml = $phiveXml;
        $this->installer = $installer;
        $this->registry = $registry;
        $this->pharService = $pharService;
    }

    /**
     * @param Release $release
     * @param VersionConstraint $versionConstraint
     * @param Filename $destination
     * @param bool $makeCopy
     */
    public function execute(Release $release, VersionConstraint $versionConstraint, Filename $destination, $makeCopy) {
        $phar = $this->pharService->getPharFromRelease($release);
        $this->installer->install($phar->getFile(), $destination, $makeCopy);
        $this->registry->addUsage($phar, $destination);

        if ($this->phiveXml->hasConfiguredPhar($release->getName(), $release->getVersion())) {
            $configuredPhar = $this->phiveXml->getConfiguredPhar($release->getName(), $release->getVersion());
            if ($configuredPhar->getVersionConstraint()->equals($versionConstraint)) {
                return;
            }

        }

        $this->phiveXml->addPhar(
            new InstalledPhar(
                $phar->getName(),
                $release->getVersion(),
                $this->getInstalledVersionConstraint($versionConstraint, $release->getVersion()),
                $destination
            )
        );
    }

    /**
     * @param VersionConstraint $requestedVersionConstraint
     * @param Version $installedVersion
     *
     * @return VersionConstraint
     */
    private function getInstalledVersionConstraint(VersionConstraint $requestedVersionConstraint, Version $installedVersion) {
        if (!$requestedVersionConstraint instanceof AnyVersionConstraint) {
            return $requestedVersionConstraint;
        }
        return new AndVersionConstraintGroup(
            sprintf('^%s', $installedVersion->getVersionString()),
            [
                new GreaterThanOrEqualToVersionConstraint($installedVersion->getVersionString(), $installedVersion),
                new SpecificMajorVersionConstraint($installedVersion->getVersionString(), $installedVersion->getMajor()->getValue())
            ]
        );
    }

}
