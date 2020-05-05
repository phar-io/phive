<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RequestedPharResolverFactory {
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    public function getPharIoAliasResolver(SourcesListFileLoader $sourcesListFileLoader): PharIoAliasResolver {
        return new PharIoAliasResolver(
            $sourcesListFileLoader,
            $this->factory->getFileDownloader()
        );
    }

    public function getUrlResolver(): DirectUrlResolver {
        return new DirectUrlResolver($this->factory->getHttpClient());
    }

    public function getLocalAliasResolver(): LocalAliasResolver {
        return new LocalAliasResolver($this->factory->getPharRegistry());
    }

    public function getRemoteSourcesListFileLoader(): RemoteSourcesListFileLoader {
        return $this->factory->getRemoteSourcesListFileLoader();
    }

    public function getGithubAliasResolver(): GithubAliasResolver {
        return $this->factory->getGithubAliasResolver();
    }

    public function getGitlabAliasResolver(): GitlabAliasResolver {
        return $this->factory->getGitlabAliasResolver();
    }

    public function getLocalSourcesListFileLoader(): LocalSourcesListFileLoader {
        return $this->factory->getLocalSourcesListFileLoader();
    }

    public function getProjectSourcesListFileLoader(): LocalSourcesListFileLoader {
        return new LocalSourcesListFileLoader(
            $this->factory->getConfig()->getProjectRepositories()
        );
    }
}
