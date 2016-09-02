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
     * @return Source
     * @throws SourcesListException
     */
    public function getSourceForAlias(PharAlias $alias) {
        $query = sprintf('//phive:phar[@alias="%s"]/phive:repository', $alias);
        $repositoryNodes = $this->sourcesFile->query($query);

        if ($repositoryNodes->length === 0) {
            throw new SourcesListException(sprintf('No repository found for alias %s', $alias));
        }
        if ($repositoryNodes->length > 1) {
            throw new SourcesListException(sprintf('Multiple repositories found for alias %s', $alias));
        }

        /** @var \DOMElement $repositoryNode */
        $repositoryNode = $repositoryNodes->item(0);
        return new Source(
            $repositoryNode->getAttribute('type') ?: 'phar.io',
            new Url($repositoryNode->getAttribute('url'))
        );
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
