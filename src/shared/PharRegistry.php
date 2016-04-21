<?php
namespace PharIo\Phive;

class PharRegistry {

    /**
     * @var Directory
     */
    private $pharDirectory;

    /**
     * @var XmlFile
     */
    private $dbFile;

    /**
     * @param XmlFile   $xmlFile
     * @param Directory $pharDirectory
     */
    public function __construct(XmlFile $xmlFile, Directory $pharDirectory) {
        $this->dbFile = $xmlFile;
        $this->pharDirectory = $pharDirectory;
    }

    /**
     * @param Phar $phar
     */
    public function addPhar(Phar $phar) {
        $this->savePhar($phar->getFile());
        $pharNode = $this->dbFile->createElement('phar');
        $pharNode->setAttribute('name', $phar->getName());
        $pharNode->setAttribute('version', $phar->getVersion()->getVersionString());
        $pharNode->setAttribute(
            'location',
            $this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFile()->getFilename()
        );
        $hashNode = $this->dbFile->createElement('hash', $phar->getFile()->getSha1Hash()->asString());
        $hashNode->setAttribute('type', 'sha1');
        $pharNode->appendChild($hashNode);

        if ($phar->hasSignatureFingerprint()) {
            $signatureNode = $this->dbFile->createElement('signature');
            $signatureNode->setAttribute('fingerprint', $phar->getSignatureFingerprint());
            $pharNode->appendChild($signatureNode);
        }

        $this->dbFile->addElement($pharNode);
        $this->dbFile->save();
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
        if ($this->dbFile->query(sprintf('//phive:usage[@destination="%s"]', $destination), $pharNode)->length
            !== 0
        ) {
            return;
        }
        $usageNode = $this->dbFile->createElement('usage');
        $usageNode->setAttribute('destination', $destination);
        $pharNode->appendChild($usageNode);
        $this->dbFile->save();
    }

    /**
     * @param string  $name
     * @param Version $version
     *
     * @return \DOMElement
     */
    private function getFirstMatchingPharNode($name, Version $version) {
        $query = sprintf('//phive:phar[@name="%s" and @version="%s"]', $name, $version->getVersionString());

        return $this->dbFile->query($query)->item(0);
    }

    /**
     * @param string  $name
     * @param Version $version
     *
     * @return Phar
     * @throws PharRegistryException
     */
    public function getPhar($name, Version $version) {
        if (!$this->hasPhar($name, $version)) {
            throw new PharRegistryException(sprintf(
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
     * @throws PharRegistryException
     */
    public function getByUsage($filename) {
        $pharNode = $this->dbFile->query(sprintf('//phive:phar[phive:usage/@destination="%s"]', $filename))->item(0);
        if (null === $pharNode) {
            throw new PharRegistryException(sprintf('No phar with usage %s found', $filename));
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
        $usageNode = $this->dbFile->query(sprintf('//phive:usage[@destination="%s"]', $destination), $pharNode)->item(0);
        $pharNode->removeChild($usageNode);
        $this->dbFile->save();
    }

    /**
     * @param Phar $phar
     */
    public function removePhar(Phar $phar) {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
        unlink($this->getPharDestination($phar->getFile()));
        $pharNode->parentNode->removeChild($pharNode);
        $this->dbFile->save();
    }

    /**
     * @param Phar $phar
     *
     * @return bool
     */
    public function hasUsages(Phar $phar) {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());

        return $this->dbFile->query('//phive:usage', $pharNode)->length > 0;
    }

    /**
     * @return Phar[]
     */
    public function getUnusedPhars() {
        $unusedPhars = [];
        foreach ($this->dbFile->query('//phive:phar[not(phive:usage)]') as $pharNode) {
            $unusedPhars[] = $this->nodetoPhar($pharNode);
        }
        return $unusedPhars;
    }

    /**
     * @param Directory $destination
     *
     * @return Phar[]
     */
    public function getUsedPharsByDestination(Directory $destination) {
        $usedPhars = [];
        $query = sprintf('//phive:phar[contains(phive:usage/@destination, "%s")]', $destination);
        foreach ($this->dbFile->query($query) as $pharNode) {
            $usedPhars[] = $this->nodetoPhar($pharNode);
        }

        return $usedPhars;
    }

    /**
     * @param string $alias
     *
     * @return array
     */
    public function getKnownSignatureFingerprints($alias) {
        $fingerprints = [];
        $query = sprintf('//phive:phar[@name="%s"]/phive:signature/@fingerprint', $alias);
        foreach ($this->dbFile->query($query) as $fingerprintNode) {
            if (in_array($fingerprintNode->nodeValue, $fingerprints)) {
                continue;
            }
            $fingerprints[] = $fingerprintNode->nodeValue;
        }
        return array_unique($fingerprints);
    }

}
