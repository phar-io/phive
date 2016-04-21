<?php
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of Phar.io repository URLs
 */
class PharIoAliasResolver extends AbstractAliasResolver {

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
        if (count($sources) > 0) {
            return $sources;
        }
        return $this->tryNext($alias);
    }

}
