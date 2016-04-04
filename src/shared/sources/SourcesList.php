<?php
namespace PharIo\Phive;

class SourcesList {

    /**
     * @var XmlFile
     */
    private $sourcesFile;

    /**
     * SourcesList constructor.
     *
     * @param XmlFile $sourcesFile
     */
    public function __construct(XmlFile $sourcesFile) {
        $this->sourcesFile = $sourcesFile;
    }

    /**
     * @param PharAlias $alias
     *
     * @return Source[]
     */
    public function getSourcesForAlias(PharAlias $alias) {
        $sources = [];
        $query = sprintf('//phive:phar[@alias="%s"]/phive:repository', $alias);
        foreach ($this->sourcesFile->query($query) as $repositoryNode) {
            /** @var \DOMElement $repositoryNode */
            $sources[] = new Source(
                $repositoryNode->getAttribute('type') ?: 'phar.io',
                new Url($repositoryNode->getAttribute('url'))
            );
        }
        return $sources;
    }

    public function getAliasForComposerAlias(ComposerAlias $alias) {
        $query = sprintf('//phive:phar[@composer="%s"]', $alias);
        $result = $this->sourcesFile->query($query);
        if ($result->length === 0) {
            throw new SourcesListException(
                sprintf('No such composer alias "%s"', $alias),
                SourcesListException::ComposerAliasNotFound
            );
        }

        /** @var \DOMElement $pharNode */
        $pharNode = $result->item(0);
        return $pharNode->getAttribute('alias');
    }

    /**
     * @return string[]
     */
    public function getAliases() {
        $result = [];
        foreach ($this->sourcesFile->query('//phive:phar') as $node) {
            /** @var \DOMElement $node */
            $result[] = $node->getAttribute('alias');
        }
        return $result;
    }

}
