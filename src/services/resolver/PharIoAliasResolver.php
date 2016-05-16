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
     * @var SourcesListFileLoader
     */
    private $loader;

    /**
     * @param SourcesListFileLoader $loader
     */
    public function __construct(SourcesListFileLoader $loader) {
        $this->loader = $loader;
    }

    /**
     * @param PharAlias $alias
     *
     * @return Source[]
     * @throws ResolveException
     */
    public function resolve(PharAlias $alias) {
        $sources = $this->getSources()->getSourcesForAlias($alias);
        if (count($sources) > 0) {
            return $sources;
        }
        return $this->tryNext($alias);
    }

    private function getSources() {
        if ($this->sources === null) {
            $this->sources = new SourcesList(
                new XmlFile(
                    $this->loader->load(),
                    'https://phar.io/repository-list',
                    'repositories'
                )
            );
        }
        return $this->sources;
    }

}
