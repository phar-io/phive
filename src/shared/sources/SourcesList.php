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
