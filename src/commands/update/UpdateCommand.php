<?php
namespace PharIo\Phive;

class UpdateCommand implements Cli\Command {

    /**
     * @var UpdateCommandConfig
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
     * @var PhiveXmlConfig
     */
    private $phiveXml;

    /**
     * @param UpdateCommandConfig $updateCommandConfig
     * @param InstallService $installService
     * @param RequestedPharResolverService $pharResolver
     * @param PhiveXmlConfig $phiveXml
     */
    public function __construct(
        UpdateCommandConfig $updateCommandConfig,
        InstallService $installService,
        RequestedPharResolverService $pharResolver,
        PhiveXmlConfig $phiveXml
    ) {
        $this->config = $updateCommandConfig;
        $this->installService = $installService;
        $this->pharResolver = $pharResolver;
        $this->phiveXml = $phiveXml;
    }

    public function execute() {
        foreach ($this->config->getRequestedPhars() as $requestedPhar) {
            $release = $this->resolveToRelease($requestedPhar);

            $this->installService->execute(
                $release,
                $requestedPhar->getVersionConstraint(),
                $this->phiveXml->getPharLocation($release->getName()),
                false
            );
        }
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return Release
     */
    private function resolveToRelease(RequestedPhar $requestedPhar) {
        $repository = $this->pharResolver->resolve($requestedPhar);
        $releases = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $releases->getLatest($requestedPhar->getVersionConstraint());
    }



}
