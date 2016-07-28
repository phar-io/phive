<?php
namespace PharIo\Phive;

class PhiveXmlConfig {

    /**
     * @var XmlFile
     */
    private $configFile;

    /**
     * @var VersionConstraintParser
     */
    private $versionConstraintParser;

    /**
     * @param XmlFile                 $configFile
     * @param VersionConstraintParser $versionConstraintParser
     */
    public function __construct(XmlFile $configFile, VersionConstraintParser $versionConstraintParser) {
        $this->configFile = $configFile;
        $this->versionConstraintParser = $versionConstraintParser;
    }

    /**
     * @param InstalledPhar $installedPhar
     */
    public function addPhar(InstalledPhar $installedPhar) {
        $name = $installedPhar->getName();
        if ($this->hasPharNode($name)) {
            $pharNode = $this->getPharNode($name);
        } else {
            $pharNode = $this->configFile->createElement('phar');
            $pharNode->setAttribute('name', $name);
            $this->configFile->addElement($pharNode);
        }

        $xmlFileDirectory = $this->configFile->getDirectory();

        $pharNode->setAttribute('version', $installedPhar->getVersionConstraint()->asString());
        $pharNode->setAttribute('installed', $installedPhar->getInstalledVersion()->getVersionString());
        $pharNode->setAttribute('location', $installedPhar->getLocation()->getRelativePathTo($xmlFileDirectory));
        $this->configFile->save();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasPhar($name) {
        return $this->hasPharNode($name);
    }

    /**
     * @param string $name
     *
     * @return Filename
     */
    public function getPharLocation($name) {
        $node = $this->getPharNode($name);
        return (new Directory($node->getAttribute('location')))->file($name);
    }

    /**
     * @param string $name
     */
    public function removePhar($name) {
        if (!$this->hasPharNode($name)) {
            return;
        }
        $pharNode = $this->getPharNode($name);
        $pharNode->parentNode->removeChild($pharNode);
        $this->configFile->save();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasPharNode($name) {
        return $this->getPharNode($name) !== null;
    }

    /**
     * @param string $name
     *
     * @return \DOMElement
     */
    private function getPharNode($name) {
        return $this->configFile->query(sprintf('//phive:phar[@name="%s"]', mb_strtolower($name)))->item(0);
    }

    /**
     * @return ConfiguredPhar[]
     */
    public function getPhars() {
        $phars = [];
        /** @var \DOMElement $pharNode */
        foreach ($this->configFile->query('//phive:phar') as $pharNode) {
            if ($pharNode->hasAttribute('url')) {
                $url = new PharUrl($pharNode->getAttribute('url'));
                $pharName = (string)$url;
                $versionConstraint = $url->getPharVersion()->getVersionString();
            } else {
                $pharName = $pharNode->getAttribute('name');
                $versionConstraint = $pharNode->getAttribute('version');
            }
            $pharVersion = null;
            if ($pharNode->hasAttribute('installed') && !empty($pharNode->getAttribute('installed'))) {
                $pharVersion = new Version($pharNode->getAttribute('installed'));
            }
            $phars[] = new ConfiguredPhar(
                $pharName,
                $this->versionConstraintParser->parse($versionConstraint),
                $pharVersion
            );
        }

        return $phars;
    }

    /**
     * @return bool
     */
    public function hasTargetDirectory() {
        return $this->getTargetDirectoryNode() !== null;
    }

    /**
     * @return Directory
     * @throws ConfigException
     */
    public function getTargetDirectory() {
        $node = $this->getTargetDirectoryNode();
        if ($node === null) {
            throw new ConfigException('Tools directory is not configured in phive.xml');
        }
        return new Directory($node->nodeValue);
    }

    /**
     * @param Directory $directory
     */
    public function setTargetDirectory(Directory $directory) {
        if (($node = $this->getTargetDirectoryNode()) === null) {
            $configurationNode = $this->configFile->query('//phive:configuration')->item(0);
            if ($configurationNode === null) {
                $configurationNode = $this->configFile->createElement('configuration');
                $this->configFile->addElement($configurationNode);
            }
            $node = $this->configFile->createElement('targetDirectory');
            $configurationNode->appendChild($node);
        }
        $xmlFileDirectory = $this->configFile->getDirectory();
        $node->nodeValue = $directory->getRelativePathTo($xmlFileDirectory);
    }

    /**
     * @return \DOMNode
     */
    private function getTargetDirectoryNode() {
        return $this->configFile->query('//phive:configuration/phive:targetDirectory[1]')->item(0);
    }
}
