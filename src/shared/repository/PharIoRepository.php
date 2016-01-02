<?php
namespace PharIo\Phive;

class PharIoRepository extends XmlRepository {

    /**
     * @param PharAlias $alias
     *
     * @return ReleaseCollection
     */
    public function getReleases(PharAlias $alias) {
        $releases = new ReleaseCollection();
        $query = sprintf('//phive:phar[@name="%s"]/phive:release', $alias);
        foreach ($this->getXPath()->query($query) as $releaseNode) {
            /** @var \DOMElement $releaseNode */
            $releases->add(
                new Release(
                    new Version($releaseNode->getAttribute('version')),
                    new Url($releaseNode->getAttribute('url')),
                    $this->getHash($releaseNode)
                )
            );
        }
        return $releases;
    }

    /**
     * @param \DOMElement $releaseNode
     *
     * @return Sha1Hash|Sha256Hash
     * @throws InvalidHashException
     */
    private function getHash(\DOMElement $releaseNode) {
        /** @var \DOMElement $hashNode */
        $hashNode = $releaseNode->getElementsByTagName('hash')->item(0);
        $type = $hashNode->getAttribute('type');
        switch ($type) {
            case 'sha-1':
                return new Sha1Hash($hashNode->getAttribute('value'));
            case 'sha-256':
                return new Sha256Hash($hashNode->getAttribute('value'));
        }
        throw new InvalidHashException(sprintf('Unsupported Hash Type %s', $type));
    }

    /**
     * @return string
     */
    protected function getRootElementName() {
        return 'repository';
    }

    /**
     * @return string
     */
    protected function getNamespace() {
        return 'https://phar.io/repository';
    }

}



