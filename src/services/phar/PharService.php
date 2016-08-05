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
     * @var AliasResolverService
     */
    private $aliasResolver;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var SourceRepositoryLoader
     */
    private $sourceRepositoryLoader;

    /**
     * @param PharDownloader         $downloader
     * @param PharInstaller          $installer
     * @param PharRegistry           $pharRegistry
     * @param AliasResolverService   $resolver
     * @param Cli\Output             $output
     * @param SourceRepositoryLoader $sourceRepositoryLoader
     */
    public function __construct(
        PharDownloader $downloader,
        PharInstaller $installer,
        PharRegistry $pharRegistry,
        AliasResolverService $resolver,
        Cli\Output $output,
        SourceRepositoryLoader $sourceRepositoryLoader
    ) {
        $this->downloader = $downloader;
        $this->installer = $installer;
        $this->pharRegistry = $pharRegistry;
        $this->aliasResolver = $resolver;
        $this->output = $output;
        $this->sourceRepositoryLoader = $sourceRepositoryLoader;
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Directory $destination
     * @param bool $makeCopy
     *
     * @return null|InstalledPhar
     */
    public function install(RequestedPhar $requestedPhar, Directory $destination, $makeCopy) {
        $release = $this->getRelease($requestedPhar);
        $pharName = $release->getUrl()->getPharName();
        $phar = $this->getPharFromRelease($release);
        $destinationFile = $destination->file($pharName);

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
        $release = $this->getRelease($requestedPhar);

        if (!$release->getVersion()->isGreaterThan($currentVersion)) {
            $this->output->writeInfo(
                sprintf(
                    '%s: %s is the newest version matching constraint %s, skipping.',
                    $requestedPhar->getAlias(),
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
     * @throws DownloadFailedException
     * @throws ResolveException
     *
     * @return Release
     */
    private function getRelease(RequestedPhar $requestedPhar) {
        if ($requestedPhar->isAlias()) {
            return $this->resolveAlias($requestedPhar->getAlias());
        }

        $url = $requestedPhar->getPharUrl();

        return new Release($url->getPharName(), $url->getPharVersion(), $url, null);
    }

    /**
     * @param PharAlias $alias
     *
     * @return Release
     * @throws InstallationFailedException
     * @throws ResolveException
     *
     */
    private function resolveAlias(PharAlias $alias) {
        foreach ($this->aliasResolver->resolve($alias) as $source) {
            try {
                $repo = $this->sourceRepositoryLoader->loadRepository($source);
                $releases = $repo->getReleasesByAlias($alias);
                return $releases->getLatest($alias->getVersionToInstall());
            } catch (ResolveException $e) {
                $this->output->writeWarning(
                    sprintf(
                        'Resolving alias %s with repository %s failed: %s',
                        $alias,
                        $source->getUrl(),
                        $e->getMessage()
                    )
                );
                continue;
            }
        }
        throw new ResolveException(sprintf('Could not resolve alias %s', $alias));
    }
}
