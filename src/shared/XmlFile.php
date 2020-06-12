<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;

class XmlFile {
    /** @var null|\DOMDocument */
    private $dom;

    /** @var \DOMXPath */
    private $xPath;

    /** @var Filename */
    private $filename;

    /** @var string */
    private $namespace;

    /** @var string */
    private $rootElementName;

    public function __construct(Filename $filename, string $namespace, string $root) {
        $this->filename        = $filename;
        $this->namespace       = $namespace;
        $this->rootElementName = $root;
        $this->init();
    }

    public function createElement(string $name, string $text = ''): \DOMElement {
        return $this->getDom()->createElementNS($this->namespace, $name, $text);
    }

    public function query(string $xpath, \DOMNode $ctx = null): \DOMNodeList {
        if ($ctx === null) {
            $ctx = $this->getDom()->documentElement;
        }

        return $this->getXPath()->query($xpath, $ctx);
    }

    public function addElement(\DOMNode $node): void {
        $this->getDom()->documentElement->appendChild($node);
    }

    public function save(): void {
        $this->getDirectory()->ensureExists();
        $this->getDom()->save($this->filename->asString());
    }

    public function getDirectory(): Directory {
        return $this->filename->getDirectory();
    }

    public function getDom(): \DOMDocument {
        return $this->dom;
    }

    /** @psalm-assert \DomDocument $this->dom */
    private function init(): void {
        if ($this->dom instanceof \DOMDocument) {
            return;
        }

        $this->dom                     = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput       = true;

        if ($this->filename->exists()) {
            $this->dom->load($this->filename->asString());
        } else {
            $this->dom->appendChild($this->dom->createElementNS($this->namespace, $this->rootElementName));
        }
        $this->xPath = new \DOMXPath($this->dom);
        $this->xPath->registerNamespace('phive', $this->namespace);
    }

    private function getXPath(): \DOMXPath {
        return $this->xPath;
    }
}
