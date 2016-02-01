<?php
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of Phar.io repository URLs
 */
class AliasResolver {

    /**
     * @var PharRepositoryList
     */
    private $repositoryList;

    /**
     * @param PharRepositoryList $repositoryList
     */
    public function __construct(PharRepositoryList $repositoryList) {
        $this->repositoryList = $repositoryList;
    }

    /**
     * @param PharAlias $alias
     *
     * @return Url[]
     * @throws ResolveException
     */
    public function resolve(PharAlias $alias) {
        $urls = $this->repositoryList->getRepositoryUrls($alias);
        if (empty($urls)) {
            throw new ResolveException(sprintf('Could not resolve alias %s', $alias));
        }
        return $urls;
    }

}
