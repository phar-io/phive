<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\AndVersionConstraintGroup;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\GreaterThanOrEqualToVersionConstraint;
use PharIo\Version\SpecificMajorVersionConstraint;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;

class InstallService {
    /** @var PhiveXmlConfig */
    private $phiveXml;

    /** @var PharInstaller */
    private $installer;

    /** @var PharRegistry */
    private $registry;

    /** @var PharService */
    private $pharService;

    /** @var CompatibilityService */
    private $compatibilityService;

    public function __construct(
        PhiveXmlConfig $phiveXml,
        PharInstaller $installer,
        PharRegistry $registry,
        PharService $pharService,
        CompatibilityService $compatibilityChecker
    ) {
        $this->phiveXml             = $phiveXml;
        $this->installer            = $installer;
        $this->registry             = $registry;
        $this->pharService          = $pharService;
        $this->compatibilityService = $compatibilityChecker;
    }

    public function execute(SupportedRelease $release, RequestedPhar $requestedPhar, Filename $destination, bool $updatePhiveXml): void {
        $versionConstraint = $requestedPhar->getVersionConstraint();
        $makeCopy          = $requestedPhar->makeCopy();
        $phar              = $this->pharService->getPharFromRelease($release);

        if (!$this->compatibilityService->canRun($phar)) {
            return;
        }

        $this->installer->install($phar->getFile(), $destination, $makeCopy);
        $this->registry->addUsage($phar, $destination);

        if ($this->phiveXml->hasConfiguredPhar($release->getName(), $release->getVersion())) {
            $configuredPhar = $this->phiveXml->getConfiguredPhar($release->getName(), $release->getVersion());

            if ($configuredPhar->getVersionConstraint()->asString() === $versionConstraint->asString()) {
                return;
            }
        }

        if ($updatePhiveXml) {
            $this->phiveXml->addPhar(
                new InstalledPhar(
                    $phar->getName(),
                    $release->getVersion(),
                    $this->getInstalledVersionConstraint($versionConstraint, $release->getVersion()),
                    $destination,
                    $makeCopy
                ),
                $requestedPhar
            );
        }
    }

    private function getInstalledVersionConstraint(VersionConstraint $requestedVersionConstraint, Version $installedVersion): VersionConstraint {
        if (!$requestedVersionConstraint instanceof AnyVersionConstraint) {
            return $requestedVersionConstraint;
        }

        return new AndVersionConstraintGroup(
            \sprintf('^%s', $installedVersion->getVersionString()),
            [
                new GreaterThanOrEqualToVersionConstraint($installedVersion->getVersionString(), $installedVersion),
                new SpecificMajorVersionConstraint($installedVersion->getVersionString(), $installedVersion->getMajor()->getValue())
            ]
        );
    }
}
