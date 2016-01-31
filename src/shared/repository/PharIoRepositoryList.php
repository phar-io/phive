<?php
namespace PharIo\Phive;

class PharIoRepositoryList extends XmlRepository {

    /**
     * @param PharAlias $alias
     *
     * @return Url[]
     */
    public function getRepositoryUrls(PharAlias $alias) {
        $urls = [];
        $query = sprintf('//phive:phar[@alias="%s"]/phive:repository', $alias);
        foreach ($this->getXPath()->query($query) as $repositoryNode) {
            /** @var \DOMElement $repositoryNode */
            $urls[] = new Url($repositoryNode->getAttribute('url'));
        }
        return $urls;
    }

    /**
     * @return string[]
     */
    public function getAliases() {
        $result = [];
        foreach ($this->getDom()->getElementsByTagName('phar') as $node) {
            $result[] = $node->attributes->getNamedItem('alias')->value;
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function getRootElementName() {
        return 'repositories';
    }

    /**
     * @return string
     */
    protected function getNamespace() {
        return 'https://phar.io/repository-list';
    }

}
