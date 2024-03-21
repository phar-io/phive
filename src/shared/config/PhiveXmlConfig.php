<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function is_dir;
use function mb_strtolower;
use function sprintf;
use DOMElement;
use DOMNode;
use Exception;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

abstract class PhiveXmlConfig {
    /** @var XmlFile */
    private $configFile;

    /** @var VersionConstraintParser */
    private $versionConstraintParser;

    public function __construct(XmlFile $configFile, VersionConstraintParser $versionConstraintParser) {
        $this->configFile              = $configFile;
        $this->versionConstraintParser = $versionConstraintParser;
    }

    /**
     * @throws Exception
     */
    public function addPhar(InstalledPhar $installedPhar, RequestedPhar $requestedPhar): void {
        $name = $installedPhar->getName();

        if ($this->hasPharNode($name)) {
            $pharNode = $this->getPharNode($name);

            if ($this->isIdentical($pharNode, $installedPhar)) {
                return;
            }
        } else {
            $pharNode = $this->configFile->createElement('phar');
            $pharNode->setAttribute('name', $name);
            $this->configFile->addElement($pharNode);
        }

        if ($requestedPhar->hasUrl()) {
            $pharNode->setAttribute('url', $requestedPhar->getUrl()->asString());
        }

        $pharNode->setAttribute('version', $installedPhar->getVersionConstraint()->asString());
        $pharNode->setAttribute('installed', $installedPhar->getInstalledVersion()->getVersionString());
        $pharNode->setAttribute('location', $this->getLocation($installedPhar)->asString());
        $pharNode->setAttribute('copy', $installedPhar->isCopy() ? 'true' : 'false');

        $this->configFile->save();
    }

    public function hasPhar(string $name): bool {
        return $this->hasPharNode($name);
    }

    public function hasPharLocation(string $name): bool {
        return $this->hasPhar($name) && $this->getPharNode($name)->hasAttribute('location');
    }

    public function getPharLocation(string $name): Filename {
        $locationAttribute = $this->getPharNode($name)->getAttribute('location');

        if (is_dir($locationAttribute)) {
            return (new Directory($locationAttribute))->file($name)->withAbsolutePath();
        }

        return (new Filename($locationAttribute))->withAbsolutePath();
    }

    public function removePhar(string $name): void {
        if (!$this->hasPharNode($name)) {
            return;
        }
        $pharNode = $this->getPharNode($name);
        $pharNode->parentNode->removeChild($pharNode);
        $this->configFile->save();
    }

    /**
     * @throws ConfigException
     */
    public function getPharVersion(RequestedPhar $phar): Version {
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

    public function isPharInstalled(RequestedPhar $phar): bool {
        $name = $phar->asString();

        if (!$this->hasPharLocation($name)) {
            return false;
        }

        return $this->getPharLocation($name)->exists() && $this->getPharNode($name)->hasAttribute('installed');
    }

    /**
     * @return ConfiguredPhar[]
     */
    public function getPhars(): array {
        $phars = [];

        /** @var DOMElement $pharNode */
        foreach ($this->configFile->query('//phive:phar') as $pharNode) {
            $phars[] = $this->nodeToConfiguredPhar($pharNode);
        }

        return $phars;
    }

    public function hasConfiguredPhar(string $name, Version $version): bool {
        return $this->getPharNodeWithSpecificInstalledVersion($name, $version) !== null;
    }

    public function getConfiguredPhar(string $name, Version $version): ConfiguredPhar {
        $pharNode = $this->getPharNodeWithSpecificInstalledVersion($name, $version);

        if ($pharNode !== null) {
            return $this->nodeToConfiguredPhar($pharNode);
        }

        throw new ConfigException(sprintf('PHAR %s not found in phive.xml', $name));
    }

    public function hasTargetDirectory(): bool {
        return $this->getTargetDirectoryNode() !== null;
    }

    /**
     * @throws ConfigException
     */
    public function getTargetDirectory(): Directory {
        $node = $this->getTargetDirectoryNode();

        if ($node === null || !is_string($node->nodeValue)) {
            throw new ConfigException('Tools directory is not configured in phive.xml');
        }

        return new Directory($node->nodeValue);
    }

    public function setTargetDirectory(Directory $directory): void {
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
        $node->nodeValue  = $directory->getRelativePathTo($xmlFileDirectory);
    }

    abstract protected function getLocation(InstalledPhar $installedPhar): Filename;

    private function hasPharNode(string $name): bool {
        return $this->getPharNode($name) !== null;
    }

    /** @psalm-ignore-nullable-return */
    private function getPharNode(string $name): ?DOMElement {
        /** @var DOMElement $pharItemNode */
        foreach ($this->configFile->query('//phive:phar') as $pharItemNode) {
            if (mb_strtolower($pharItemNode->getAttribute('name')) === mb_strtolower($name)) {
                return $pharItemNode;
            }
        }

        return null;
    }

    private function getPharNodeWithSpecificInstalledVersion(string $name, Version $version): ?DOMElement {
        /** @var DOMElement $pharItemNode */
        foreach ($this->configFile->query('//phive:phar') as $pharItemNode) {
            if (mb_strtolower($pharItemNode->getAttribute('name')) === mb_strtolower($name) &&
                $pharItemNode->getAttribute('version') === $version->getVersionString()) {
                return $pharItemNode;
            }
        }

        return null;
    }

    private function nodeToConfiguredPhar(DOMElement $pharNode): ConfiguredPhar {
        $url = null;

        if ($pharNode->hasAttribute('url')) {
            $url               = new PharUrl($pharNode->getAttribute('url'));
            $pharName          = $url->asString();
            $versionConstraint = $url->getPharVersion()->getVersionString();
        } else {
            $pharName          = $pharNode->getAttribute('name');
            $versionConstraint = $pharNode->getAttribute('version');
        }
        $pharVersion = null;

        if ($pharNode->hasAttribute('installed') && !empty($pharNode->getAttribute('installed'))) {
            $pharVersion = new Version($pharNode->getAttribute('installed'));
        }
        $location = null;

        if ($pharNode->hasAttribute('location') && !empty($pharNode->getAttribute('location'))) {
            $filename = new Filename($pharNode->getAttribute('location'));
            $filename->getDirectory()->ensureExists();
            $location = $filename->withAbsolutePath();
        }

        $isCopy = $pharNode->hasAttribute('copy') && $pharNode->getAttribute('copy') === 'true';

        return new ConfiguredPhar(
            $pharName,
            $this->versionConstraintParser->parse($versionConstraint),
            $pharVersion,
            $location,
            $url,
            $isCopy
        );
    }

    private function getTargetDirectoryNode(): ?DOMNode {
        return $this->configFile->query('//phive:configuration/phive:targetDirectory[1]')->item(0);
    }

    private function isIdentical(DOMElement $pharNode, InstalledPhar $installedPhar): bool {
        return
            $pharNode->getAttribute('version') === $installedPhar->getVersionConstraint()->asString() &&
            $pharNode->getAttribute('installed') === $installedPhar->getInstalledVersion()->getVersionString() &&
            $pharNode->getAttribute('location') === $this->getLocation($installedPhar)->asString() &&
            $pharNode->getAttribute('copy') == ($installedPhar->isCopy() ? 'true' : 'false');
    }
}
