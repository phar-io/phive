<?php declare(strict_types = 1);
namespace PharIo\Phive;

class UpdateCommand implements Cli\Command {

    /** @var UpdateCommandConfig */
    private $config;

    /** @var InstallService */
    private $installService;

    /** @var RequestedPharResolverService */
    private $pharResolver;

    /** @var PhiveXmlConfig */
    private $phiveXml;

    /** @var ReleaseSelector */
    private $selector;

    public function __construct(
        UpdateCommandConfig $updateCommandConfig,
        InstallService $installService,
        RequestedPharResolverService $pharResolver,
        PhiveXmlConfig $phiveXml,
        ReleaseSelector $selector
    ) {
        $this->config         = $updateCommandConfig;
        $this->installService = $installService;
        $this->pharResolver   = $pharResolver;
        $this->phiveXml       = $phiveXml;
        $this->selector       = $selector;
    }

    public function execute(): void {
        foreach ($this->config->getRequestedPhars() as $requestedPhar) {
            $release = $this->resolveToRelease($requestedPhar);

            $this->installService->execute(
                $release,
                $requestedPhar,
                $this->phiveXml->getPharLocation($release->getName()),
                true
            );
        }
    }

    private function resolveToRelease(RequestedPhar $requestedPhar): SupportedRelease {
        $repository = $this->pharResolver->resolve($requestedPhar);
        $releases   = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $this->selector->select($releases, $requestedPhar->getVersionConstraint(), $this->config->forceAcceptUnsignedPhars());
    }
}
