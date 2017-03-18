<?php
namespace PharIo\Phive;

class AbstractResolvingStrategy implements ResolvingStrategy {

    /**
     * @var RequestedPharResolverFactory
     */
    private $factory;

    /**
     * @param RequestedPharResolverFactory $factory
     */
    public function __construct(RequestedPharResolverFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param RequestedPharResolverService $service
     */
    public function execute(RequestedPharResolverService $service) {
        // github.com
        $service->addResolver($this->factory->getGithubAliasResolver());

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

    /**
     * @return RequestedPharResolverFactory
     */
    protected function getFactory() {
        return $this->factory;
    }

}
