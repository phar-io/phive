<?php
namespace PharIo\Phive;

class PharRepository extends WritableXmlRepository {

    /**
     * @var Directory
     */
    private $pharDirectory;

    /**
     * @param Filename  $filename
     * @param Directory $pharDirectory
     */
    public function __construct(Filename $filename, Directory $pharDirectory) {
        $this->pharDirectory = $pharDirectory;
        parent::__construct($filename);
    }

    /**
     * @param Phar $phar
     */
    public function addPhar(Phar $phar) {
        $this->savePhar($phar->getFile());
        $dom = $this->getDom();
        $pharNode = $dom->createElement('phar');
        $pharNode->setAttribute('name', $phar->getName());
        $pharNode->setAttribute('version', $phar->getVersion()->getVersionString());
        $pharNode->setAttribute(
            'location',
            $this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFile()->getFilename()
        );
        $hashNode = $dom->createElement('hash', $phar->getFile()->getSha1Hash()->asString());
        $hashNode->setAttribute('type', 'sha1');
        $pharNode->appendChild($hashNode);
        $dom->firstChild->appendChild($pharNode);
        $this->save();
    }

    /**
     * @todo this belongs elsewhere
     *
     * @param File $pharFile
     */
    private function savePhar(File $pharFile) {
        $destination = $this->getPharDestination($pharFile);
        file_put_contents($destination, $pharFile->getContent());
        chmod($destination, 0755);
    }

    /**
     * @param File $file
     *
     * @return string
     */
    private function getPharDestination(File $file) {
        return $this->pharDirectory . DIRECTORY_SEPARATOR . $file->getFilename();
    }

    /**
     * @param Phar   $phar
     * @param string $destination
     */
    public function addUsage(Phar $phar, $destination) {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
        if ($this->getXPath()->query(sprintf('//usage[@destination="%s"]', $destination), $pharNode)->length
            !== 0
        ) {
            return;
        }
        $usageNode = $this->getDom()->createElement('usage');
        $usageNode->setAttribute('destination', $destination);
        $pharNode->appendChild($usageNode);
        $this->save();
    }

    /**
     * @param string  $name
     * @param Version $version
     *
     * @return \DOMElement
     */
    private function getFirstMatchingPharNode($name, Version $version) {
        $query = sprintf('//phar[@name="%s" and @version="%s"]', $name, $version->getVersionString());

        return $this->getXPath()->query($query)->item(0);
    }

    /**
     * @param string  $name
     * @param Version $version
     *
     * @return Phar
     * @throws PharRepositoryException
     */
    public function getPhar($name, Version $version) {
        if (!$this->hasPhar($name, $version)) {
            throw new PharRepositoryException(sprintf(
                'Phar %s %s not found in database.',
                $name,
                $version->getVersionString()
            ));
        }

        return $this->nodetoPhar($this->getFirstMatchingPharNode($name, $version));
    }

    /**
     * @param string  $name
     * @param Version $version
     *
     * @return bool
     */
    public function hasPhar($name, Version $version) {
        return $this->getFirstMatchingPharNode($name, $version) !== null;
    }

    /**
     * @param \DOMElement $pharNode
     *
     * @return Phar
     */
    private function nodetoPhar(\DOMElement $pharNode) {
        return new Phar(
            $pharNode->getAttribute('name'),
            new Version($pharNode->getAttribute('version')),
            $this->loadPharFile($pharNode->getAttribute('location'))
        );
    }

    /**
     * @param string $filename
     *
     * @return File
     */
    private function loadPharFile($filename) {
        return new File(new Filename(pathinfo($filename, PATHINFO_BASENAME)), file_get_contents($filename));
    }

    /**
     * @param string $filename
     *
     * @return Phar
     * @throws PharRepositoryException
     */
    public function getByUsage($filename) {
        $pharNode = $this->getXPath()->query(sprintf('//phar[usage/@destination="%s"]', $filename))->item(0);
        if (null === $pharNode) {
            throw new PharRepositoryException(sprintf('No phar with usage %s found', $filename));
        }

        /** @var \DOMElement $pharNode */
        return $this->nodetoPhar($pharNode);
    }

    /**
     * @param Phar   $phar
     * @param string $destination
     */
    public function removeUsage(Phar $phar, $destination) {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
        $usageNode = $this->getXPath()->query(sprintf('//usage[@destination="%s"]', $destination), $pharNode)->item(0);
        $pharNode->removeChild($usageNode);
        $this->save();
    }

    /**
     * @param Phar $phar
     */
    public function removePhar(Phar $phar) {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
        unlink($this->getPharDestination($phar->getFile()));
        $pharNode->parentNode->removeChild($pharNode);
        $this->save();
    }

    /**
     * @param Phar $phar
     *
     * @return bool
     */
    public function hasUsages(Phar $phar) {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());

        return $this->getXPath()->query('//usage', $pharNode)->length > 0;
    }

    /**
     * @return Phar[]
     */
    public function getUnusedPhars() {
        $unusedPhars = [];
        foreach ($this->getXPath()->query('//phar[count(usage) = 0]') as $pharNode) {
            $unusedPhars[] = $this->nodetoPhar($pharNode);
        }
        return $unusedPhars;
    }

    /**
     * @return string
     */
    protected function getRootElementName() {
        return 'phars';
    }

    /**
     * @return string
     */
    protected function getNamespace() {
        return '';
    }

}
