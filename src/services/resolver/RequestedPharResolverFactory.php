<?php
namespace PharIo\Phive;

class RequestedPharResolverFactory {

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param SourcesListFileLoader $sourcesListFileLoader
     *
     * @return PharIoAliasResolver
     */
    public function getPharIoAliasResolver(SourcesListFileLoader $sourcesListFileLoader) {
        return new PharIoAliasResolver(
            $sourcesListFileLoader,
            $this->factory->getFileDownloader()
        );
    }

    /**
     * @return DirectUrlResolver
     */
    public function getUrlResolver() {
        return new DirectUrlResolver();
    }

    /**
     * @return LocalAliasResolver
     */
    public function getLocalAliasResolver() {
        return new LocalAliasResolver($this->factory->getPharRegistry());
    }

    /**
     * @return RemoteSourcesListFileLoader
     */
    public function getRemoteSourcesListFileLoader() {
        return $this->factory->getRemoteSourcesListFileLoader();
    }

    /**
     * @return GithubAliasResolver
     */
    public function getGithubAliasResolver() {
        return $this->factory->getGithubAliasResolver();
    }

    /**
     * @return LocalSourcesListFileLoader
     */
    public function getLocalSourcesListFileLoader() {
        return new LocalSourcesListFileLoader(
            $this->factory->getConfig()->getHomeDirectory()->file('local.xml')
        );
    }

}
