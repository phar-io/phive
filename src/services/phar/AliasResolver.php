<?php
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of Phar.io repository URLs
 */
class AliasResolver {

    /**
     * @var SourcesList
     */
    private $sources;

    /**
     * @param SourcesList $sourcesList
     *
     */
    public function __construct(SourcesList $sourcesList) {
        $this->sources = $sourcesList;
    }

    /**
     * @param PharAlias $alias
     *
     * @return Source[]
     * @throws ResolveException
     */
    public function resolve(PharAlias $alias) {
        $sources = $this->sources->getSourcesForAlias($alias);
        if (empty($sources)) {
            throw new ResolveException(sprintf('Could not resolve alias %s', $alias));
        }
        return $sources;
    }

}
