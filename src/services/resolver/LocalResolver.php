<?php
namespace PharIo\Phive;

/**
 * Resolve Alias using a local mapping (~/.phive/local.xml)
 */
class LocalResolver extends AbstractAliasResolver {

    /**
     * @var SourcesList
     */
    private $sourcesList;

    /**
     * @param SourcesList $sourcesList
     *
     */
    public function __construct(SourcesList $sourcesList) {
        $this->sourcesList = $sourcesList;
    }

    /**
     * @param PharAlias $alias
     *
     * @return Source[]
     */
    public function resolve(PharAlias $alias) {
        $sources = $this->sourcesList->getSourcesForAlias($alias);
        if (count($sources) > 0) {
            return $sources;
        }
        return $this->tryNext($alias);
    }
}
