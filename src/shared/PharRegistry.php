<?php declare(strict_types = 1);
namespace PharIo\Phive;

use DOMElement;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Version\Version;

class PharRegistry {

    /** @var Directory */
    private $pharDirectory;

    /** @var XmlFile */
    private $dbFile;

    public function __construct(XmlFile $xmlFile, Directory $pharDirectory) {
        $this->dbFile        = $xmlFile;
        $this->pharDirectory = $pharDirectory;
    }

    public function addPhar(Phar $phar): Phar {
        $destinationFile = $this->savePhar($phar);
        $pharNode        = $this->dbFile->createElement('phar');
        $pharNode->setAttribute('name', $phar->getName());
        $pharNode->setAttribute('version', $phar->getVersion()->getVersionString());
        $pharNode->setAttribute('location', $destinationFile->asString());
        $hashNode = $this->dbFile->createElement('hash', Sha1Hash::forContent($phar->getFile()->getContent())->asString());
        $hashNode->setAttribute('type', 'sha1');
        $pharNode->appendChild($hashNode);

        if ($phar->hasSignatureFingerprint()) {
            $signatureNode = $this->dbFile->createElement('signature');
            $signatureNode->setAttribute('fingerprint', $phar->getSignatureFingerprint());
            $pharNode->appendChild($signatureNode);
        }

        $this->dbFile->addElement($pharNode);
        $this->dbFile->save();

        return new Phar(
            $phar->getName(),
            $phar->getVersion(),
            new File($destinationFile, $phar->getFile()->getContent()),
            $phar->hasSignatureFingerprint() ? $phar->getSignatureFingerprint() : null
        );
    }

    /**
     * @param string $name
     *
     * @return Phar[]
     */
    public function getPhars($name): array {
        $phars = [];

        foreach ($this->dbFile->query(\sprintf('//phive:phar[@name="%s"]', $name)) as $pharNode) {
            $phars[] = $this->nodetoPhar($pharNode);
        }

        return $phars;
    }

    public function addUsage(Phar $phar, Filename $destination): void {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());

        if ($this->dbFile->query(\sprintf('//phive:usage[@destination="%s"]', $destination->asString()), $pharNode)->length
            !== 0
        ) {
            return;
        }
        $usageNode = $this->dbFile->createElement('usage');
        $usageNode->setAttribute('destination', $destination->asString());
        $pharNode->appendChild($usageNode);
        $this->dbFile->save();
    }

    /**
     * @throws PharRegistryException
     */
    public function getPhar(string $name, Version $version): Phar {
        if (!$this->hasPhar($name, $version)) {
            throw new PharRegistryException(\sprintf(
                'Phar %s %s not found in database.',
                $name,
                $version->getVersionString()
            ));
        }

        return $this->nodetoPhar($this->getFirstMatchingPharNode($name, $version));
    }

    public function hasPhar(string $name, Version $version): bool {
        return $this->getFirstMatchingPharNode($name, $version) !== null;
    }

    /**
     * @throws PharRegistryException
     */
    public function getByUsage(Filename $filename): Phar {
        $pharNode = $this->dbFile->query(\sprintf('//phive:phar[phive:usage/@destination="%s"]', $filename))->item(0);

        if (null === $pharNode) {
            throw new PharRegistryException(\sprintf('No phar with usage %s found', $filename));
        }

        /* @var DOMElement $pharNode */
        return $this->nodetoPhar($pharNode);
    }

    public function removeUsage(Phar $phar, Filename $destination): void {
        $pharNode  = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
        $usageNode = $this->dbFile->query(\sprintf('//phive:usage[@destination="%s"]', $destination), $pharNode)->item(0);
        $pharNode->removeChild($usageNode);
        $this->dbFile->save();
    }

    public function removePhar(Phar $phar): void {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
        $phar->getFile()->getFilename()->delete();
        $pharNode->parentNode->removeChild($pharNode);
        $this->dbFile->save();
    }

    public function hasUsages(Phar $phar): bool {
        $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());

        return $this->dbFile->query('phive:usage', $pharNode)->length > 0;
    }

    /**
     * @return Phar[]
     */
    public function getUnusedPhars(): array {
        $unusedPhars = [];

        foreach ($this->dbFile->query('//phive:phar[not(phive:usage)]') as $pharNode) {
            $unusedPhars[] = $this->nodetoPhar($pharNode);
        }

        return $unusedPhars;
    }

    /**
     * @return Phar[]
     */
    public function getUsedPharsByDestination(Directory $destination): array {
        $usedPhars = [];
        $query     = \sprintf('//phive:phar[contains(phive:usage/@destination, "%s")]', $destination);

        foreach ($this->dbFile->query($query) as $pharNode) {
            $usedPhars[] = $this->nodetoPhar($pharNode);
        }

        return $usedPhars;
    }

    public function getKnownSignatureFingerprints(string $alias): array {
        $fingerprints = [];
        $query        = \sprintf('//phive:phar[@name="%s"]/phive:signature/@fingerprint', $alias);

        foreach ($this->dbFile->query($query) as $fingerprintNode) {
            if (\in_array($fingerprintNode->nodeValue, $fingerprints)) {
                continue;
            }
            $fingerprints[] = $fingerprintNode->nodeValue;
        }

        return \array_unique($fingerprints);
    }

    private function savePhar(Phar $phar): Filename {
        $destination = new Filename($this->getPharDestination($phar));

        $targetDir = $destination->getDirectory();

        if (!$targetDir->isWritable()) {
            throw new FileNotWritableException(
                \sprintf('Cannot write phar to %s', (string)$targetDir)
            );
        }

        $phar->getFile()->saveAs($destination);
        \chmod($destination->asString(), 0755);

        return $destination;
    }

    private function getPharDestination(Phar $phar): string {
        return \sprintf(
            '%s/%s-%s.phar',
            $this->pharDirectory,
            $phar->getName(),
            $phar->getVersion()->getVersionString()
        );
    }

    private function getFirstMatchingPharNode(string $name, Version $version): ?DOMElement {
        $query = \sprintf('//phive:phar[@name="%s" and @version="%s"]', $name, $version->getVersionString());

        return $this->dbFile->query($query)->item(0);
    }

    private function nodetoPhar(DOMElement $pharNode): Phar {
        return new Phar(
            $pharNode->getAttribute('name'),
            new Version($pharNode->getAttribute('version')),
            $this->loadPharFile($pharNode->getAttribute('location'))
        );
    }

    private function loadPharFile(string $filename): File {
        return new File(new Filename($filename), \file_get_contents($filename));
    }
}
