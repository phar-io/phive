<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli;

class InstallCommand implements Cli\Command {

    /**
     * @var InstallCommandConfig
     */
    private $config;

    /**
     * @var InstallService
     */
    private $installService;

    /**
     * @var RequestedPharResolverService
     */
    private $pharResolver;

    /**
     * @var ReleaseSelector
     */
    private $selector;

    /**
     * @param InstallCommandConfig         $config
     * @param InstallService               $installService
     * @param RequestedPharResolverService $pharResolver
     */
    public function __construct(
        InstallCommandConfig $config,
        InstallService $installService,
        RequestedPharResolverService $pharResolver,
        ReleaseSelector $selector
    ) {
        $this->config = $config;
        $this->installService = $installService;
        $this->pharResolver = $pharResolver;
        $this->selector = $selector;
    }

    public function execute() {
        $targetDirectory = $this->getConfig()->getTargetDirectory();

        foreach ($this->getConfig()->getRequestedPhars() as $requestedPhar) {
            $this->installRequestedPhar($requestedPhar, $targetDirectory);
        }
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Directory     $targetDirectory
     */
    protected function installRequestedPhar(RequestedPhar $requestedPhar, Directory $targetDirectory) {
        $release = $this->resolveToRelease($requestedPhar);
        $destination = $this->getDestination($release->getUrl()->getPharName(), $requestedPhar, $targetDirectory);

        $this->installService->execute($release, $requestedPhar, $destination);
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SupportedRelease
     */
    private function resolveToRelease(RequestedPhar $requestedPhar) {
        $repository = $this->pharResolver->resolve($requestedPhar);
        $releases = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $this->selector->select($releases, $requestedPhar->getLockedVersion());
    }

    /**
     * @param string        $pharName
     * @param RequestedPhar $requestedPhar
     * @param Directory     $destination
     *
     * @return Filename
     */
    private function getDestination($pharName, RequestedPhar $requestedPhar, Directory $destination) {
        if ($requestedPhar->hasLocation()) {
            return $requestedPhar->getLocation();
        }

        return $destination->file($pharName);
    }

    /**
     * @return InstallCommandConfig
     */
    protected function getConfig() {
        return $this->config;
    }

}
