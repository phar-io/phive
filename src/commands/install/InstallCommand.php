<?php
namespace PharIo\Phive;

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
     * @var Environment
     */
    private $environment;

    /**
     * @var RequestedPharResolverService
     */
    private $pharResolver;

    /**
     * @param InstallCommandConfig $config
     * @param InstallService $installService
     * @param Environment $environment
     * @param RequestedPharResolverService $pharResolver
     */
    public function __construct(
        InstallCommandConfig $config,
        InstallService $installService,
        Environment $environment,
        RequestedPharResolverService $pharResolver
    ) {
        $this->config = $config;
        $this->installService = $installService;
        $this->environment = $environment;
        $this->pharResolver = $pharResolver;
    }

    public function execute() {
        $targetDirectory = $this->getTargetDirectory();

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

        $this->installService->execute($release, $requestedPhar->getVersionConstraint(), $destination, false);
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return Release
     */
    private function resolveToRelease(RequestedPhar $requestedPhar) {
        $repository = $this->pharResolver->resolve($requestedPhar);
        $releases = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $releases->getLatest($requestedPhar->getLockedVersion());
    }

    /**
     * @param string $pharName
     * @param RequestedPhar $requestedPhar
     * @param Directory $destination
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
     * @return Directory
     */
    protected function getTargetDirectory() {
        if ($this->getConfig()->installGlobally()) {
            return new Directory(dirname($this->environment->getBinaryName()));
        }
        return $this->getConfig()->getTargetDirectory();
    }

    /**
     * @return InstallCommandConfig
     */
    protected function getConfig() {
        return $this->config;
    }

}
