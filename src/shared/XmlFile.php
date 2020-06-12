<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
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

    public static function fromFile(File $file): self {
        $dom = self::createDomDocument();
        $dom->loadXML($file->getContent());

        $xmlFile = new self(
            $file->getFilename(),
            $dom->documentElement->namespaceURI ?? '',
            $dom->documentElement->localName
        );
        $xmlFile->dom = $dom;

        return $xmlFile;
    }

    public function __construct(Filename $filename, string $namespace, string $root) {
        $this->filename        = $filename;
        $this->namespace       = $namespace;
        $this->rootElementName = $root;
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
        $this->initDom();

        return $this->dom;
    }

    private function getXPath(): \DOMXPath {
        $this->initXPath();

        return $this->xPath;
    }

    /** @psalm-assert \DomDocument $this->dom */
    private function initDom(): void {
        if ($this->dom instanceof \DOMDocument) {
            return;
        }

        $this->dom = self::createDomDocument();

        if ($this->filename->exists()) {
            $this->dom->load($this->filename->asString());
        } else {
            $this->dom->appendChild($this->dom->createElementNS($this->namespace, $this->rootElementName));
        }
    }

    private function initXPath(): void {
        $this->initDom();

        $this->xPath = new \DOMXPath($this->dom);
        $this->xPath->registerNamespace('phive', $this->namespace);
    }

    private static function createDomDocument(): \DOMDocument {
        $dom                     = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;

        return $dom;
    }
}
