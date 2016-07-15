<?php
namespace PharIo\Phive;

/**
 * Resolve Alias using a local mapping (~/.phive/local.xml)
 */
class LocalResolver extends AbstractAliasResolver {

    /**
     * @var Filename
     */
    private $filename;

    /**
     * @var SourcesList
     */
    private $sources;

    /**
     * LocalResolver constructor.
     *
     * @param Filename $filename
     */
    public function __construct(Filename $filename) {
        $this->filename = $filename;
    }

    /**
     * @param PharAlias $alias
     *
     * @return Source[]
     */
    public function resolve(PharAlias $alias) {
        if (!$this->filename->exists()) {
            return $this->tryNext($alias);
        }

        $sources = $this->getSources()->getSourcesForAlias($alias);
        if (count($sources) > 0) {
            return $sources;
        }
        return $this->tryNext($alias);
    }

    /**
     * @return SourcesList
     */
    private function getSources() {
        if (!$this->sources) {
            $this->sources = new SourcesList(
                new XmlFile(
                    $this->filename,
                    'https://phar.io/repository-list',
                    'repositories'
                )
            );
        }
        return $this->sources;
    }

}
