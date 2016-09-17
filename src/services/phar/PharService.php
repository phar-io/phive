<?php
namespace PharIo\Phive;

class PharService {

    /**
     * @var PharDownloader
     */
    private $downloader;

    /**
     * @var PharInstaller
     */
    private $installer;

    /**
     * @var PharRegistry
     */
    private $pharRegistry;

    /**
     * @var RequestedPharResolverService
     */
    private $requestedPharResolver;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param PharDownloader $downloader
     * @param PharInstaller $installer
     * @param PharRegistry $pharRegistry
     * @param RequestedPharResolverService $requestedPharResolver
     * @param Cli\Output $output
     */
    public function __construct(
        PharDownloader $downloader,
        PharInstaller $installer,
        PharRegistry $pharRegistry,
        RequestedPharResolverService $requestedPharResolver,
        Cli\Output $output
    ) {
        $this->downloader = $downloader;
        $this->installer = $installer;
        $this->pharRegistry = $pharRegistry;
        $this->requestedPharResolver = $requestedPharResolver;
        $this->output = $output;
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Directory $destination
     * @param bool $makeCopy
     *
     * @return null|InstalledPhar
     */
    public function install(RequestedPhar $requestedPhar, Directory $destination, $makeCopy) {
        $release = $this->resolveToRelease($requestedPhar);
        $pharName = $release->getUrl()->getPharName();
        $phar = $this->getPharFromRelease($release);

        if ($requestedPhar->hasLocation()) {
            $destinationFile = $requestedPhar->getLocation();
        } else {
            $destinationFile = $destination->file($pharName);
        }

        $this->installer->install($phar->getFile(), $destinationFile, $makeCopy);
        $this->pharRegistry->addUsage($phar, $destinationFile);

        return new InstalledPhar(
            $release->getName(),
            $release->getVersion(),
            $requestedPhar->getVersionConstraint(),
            $destinationFile
        );
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Filename $location
     * @param Version $currentVersion
     *
     * @return InstalledPhar|null
     */
    public function update(RequestedPhar $requestedPhar, Filename $location, Version $currentVersion) {
        $release = $this->resolveToRelease($requestedPhar);

        if ($requestedPhar->getVersionConstraint()->complies($currentVersion)
            && !$release->getVersion()->isGreaterThan($currentVersion)
        ) {
            $this->output->writeInfo(
                sprintf(
                    '%s: %s is the newest version matching constraint %s, skipping.',
                    $requestedPhar->getAlias()->asString(),
                    $currentVersion->getVersionString(),
                    $requestedPhar->getVersionConstraint()->asString()
                )
            );

            return null;
        }

        $phar = $this->getPharFromRelease($release);
        $this->installer->install($phar->getFile(), $location, false);
        $this->pharRegistry->addUsage($phar, $location);

        return new InstalledPhar(
            $release->getName(),
            $release->getVersion(),
            $requestedPhar->getVersionConstraint(),
            $location
        );
    }

    /**
     * @param Release $release
     *
     * @return Phar
     */
    private function getPharFromRelease(Release $release) {

        if ($this->pharRegistry->hasPhar($release->getName(), $release->getVersion())) {
            return $this->pharRegistry->getPhar($release->getName(), $release->getVersion());
        }
        $phar = $this->downloader->download($release);
        $this->pharRegistry->addPhar($phar);

        return $phar;
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return Release
     */
    private function resolveToRelease(RequestedPhar $requestedPhar) {
        $repository = $this->requestedPharResolver->resolve($requestedPhar);
        $releases = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $releases->getLatest($requestedPhar->getLockedVersion());
    }
}
