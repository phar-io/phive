<?php
namespace PharIo\Phive;

class PharIoRepository implements SourceRepository {

    /**
     * @var XmlFile
     */
    private $xmlFile;

    /**
     * PharIoRepository constructor.
     *
     * @param XmlFile $xmlFile
     */
    public function __construct(XmlFile $xmlFile) {
        $this->xmlFile = $xmlFile;
    }

    /**
     * @param PharAlias $alias
     *
     * @return ReleaseCollection
     */
    public function getReleasesByAlias(PharAlias $alias) {
        $releases = new ReleaseCollection();
        $query = sprintf('//phive:phar[@name="%s"]/phive:release', $alias);
        foreach ($this->xmlFile->query($query) as $releaseNode) {
            /** @var \DOMElement $releaseNode */
            $releases->add(
                new Release(
                    (string)$alias,
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

}
