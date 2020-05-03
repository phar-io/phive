<?php declare(strict_types = 1);
namespace PharIo\Phive;

class AbstractResolvingStrategy implements ResolvingStrategy {
    /** @var RequestedPharResolverFactory */
    private $factory;

    public function __construct(RequestedPharResolverFactory $factory) {
        $this->factory = $factory;
    }

    public function execute(RequestedPharResolverService $service): void {
        // project repository
        $service->addResolver(
            $this->factory->getPharIoAliasResolver($this->factory->getProjectSourcesListFileLoader())
        );

        // github.com
        $service->addResolver($this->factory->getGithubAliasResolver());

        // gitlab.com
        $service->addResolver($this->factory->getGitlabAliasResolver());

        // local repository XML
        $service->addResolver(
            $this->factory->getPharIoAliasResolver($this->factory->getLocalSourcesListFileLoader())
        );

        // phar.io
        $service->addResolver(
            $this->factory->getPharIoAliasResolver($this->factory->getRemoteSourcesListFileLoader())
        );

        // direct URLs
        $service->addResolver($this->factory->getUrlResolver());
    }

    protected function getFactory(): RequestedPharResolverFactory {
        return $this->factory;
    }
}
