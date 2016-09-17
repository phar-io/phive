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
     * @param RequestedPhar $requestedPhar
     *
     * @return ReleaseCollection
     */
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar) {
        $releases = new ReleaseCollection();
        $query = sprintf('//phive:phar[@name="%s"]/phive:release', $requestedPhar->getAlias()->asString());
        foreach ($this->xmlFile->query($query) as $releaseNode) {
            /** @var \DOMElement $releaseNode */
            $releases->add(
                new Release(
                    $requestedPhar->getAlias()->asString(),
                    new Version($releaseNode->getAttribute('version')),
                    new PharUrl($releaseNode->getAttribute('url')),
                    $this->getSignatureUrl($releaseNode),
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

    private function getSignatureUrl(\DOMElement $releaseNode) {
        /** @var \DOMElement $signatureNode */
        $signatureNode = $releaseNode->getElementsByTagName('signature')->item(0);
        if ($signatureNode->hasAttribute('url')) {
            return new Url($signatureNode->getAttribute('url'));
        }
        return new Url($releaseNode->getAttribute('url') . '.asc');
    }

}
