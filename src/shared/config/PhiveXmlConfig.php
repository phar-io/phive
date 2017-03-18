<?php
namespace PharIo\Phive;

use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

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
     * @param XmlFile $configFile
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
     * @return bool
     */
    public function hasPharLocation($name) {
        return $this->hasPhar($name) && $this->getPharNode($name)->hasAttribute('location');
    }

    /**
     * @param string $name
     *
     * @return Filename
     */
    public function getPharLocation($name) {
        $locationAttribute = $this->getPharNode($name)->getAttribute('location');
        if (is_dir($locationAttribute)) {
            return (new Directory($locationAttribute))->file($name)->withAbsolutePath();
        }
        return (new Filename($locationAttribute))->withAbsolutePath();
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
     * @param RequestedPhar $phar
     *
     * @return Version
     * @throws ConfigException
     */
    public function getPharVersion(RequestedPhar $phar) {
        $name = $phar->asString();
        if (!$this->hasPharNode($name)) {
            throw new ConfigException(sprintf('PHAR %s not found in phive.xml', $name));
        }
        $pharNode = $this->getPharNode($name);
        if (!$pharNode->hasAttribute('installed')) {
            throw new ConfigException(sprintf('PHAR %s has no installed version', $name));
        }
        return new Version($pharNode->getAttribute('installed'));
    }

    /**
     * @param RequestedPhar $phar
     *
     * @return bool
     */
    public function isPharInstalled(RequestedPhar $phar) {
        $name = $phar->asString();
        if (!$this->hasPharLocation($name)) {
            return false;
        }
        return $this->getPharLocation($name)->exists() && $this->getPharNode($name)->hasAttribute('installed');
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
     * @param string $name
     * @param Version $version
     *
     * @return \DOMElement
     */
    private function getPharNodeWithSpecificInstalledVersion($name, Version $version) {
        return $this->configFile->query(
            sprintf('//phive:phar[@name="%s" and @installed="%s"]', mb_strtolower($name), $version->getVersionString())
        )->item(0);
    }

    /**
     * @return ConfiguredPhar[]
     */
    public function getPhars() {
        $phars = [];
        /** @var \DOMElement $pharNode */
        foreach ($this->configFile->query('//phive:phar') as $pharNode) {
            $phars[] =$this->nodeToConfiguredPhar($pharNode);
        }

        return $phars;
    }

    /**
     * @param string $name
     * @param Version $version
     *
     * @return bool
     */
    public function hasConfiguredPhar($name, Version $version) {
        return $this->getPharNodeWithSpecificInstalledVersion($name, $version) !== null;
    }

    /**
     * @param string $name
     * @param Version $version
     *
     * @return ConfiguredPhar
     */
    public function getConfiguredPhar($name, Version $version) {
        return $this->nodeToConfiguredPhar($this->getPharNodeWithSpecificInstalledVersion($name, $version));
    }

    private function nodeToConfiguredPhar(\DOMElement $pharNode) {
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
        $location = null;
        if ($pharNode->hasAttribute('location') && !empty($pharNode->getAttribute('location'))) {
            $location = new Filename($pharNode->getAttribute('location'));
            // workaround to make sure the directory gets created
            $location->getDirectory();
        }
        return new ConfiguredPhar(
            $pharName,
            $this->versionConstraintParser->parse($versionConstraint),
            $pharVersion,
            $location
        );
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
