<?php
namespace PharIo\Phive;

class XmlFile {

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DOMXPath
     */
    private $xPath;

    /**
     * @var Filename
     */
    private $filename;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $rootElementName;

    /**
     * XmlFile constructor.
     *
     * @param Filename $filename
     * @param string   $namespace
     * @param string   $root
     */
    public function __construct(Filename $filename, $namespace, $root) {
        $this->filename = $filename;
        $this->namespace = $namespace;
        $this->rootElementName = $root;
    }

    /**
     * @param string $name
     * @param string $text
     *
     * @return \DOMElement
     */
    public function createElement($name, $text = null) {
        return $this->getDom()->createElementNS($this->namespace, $name, $text);
    }

    /**
     * @param string   $xpath
     * @param \DOMNode $ctx
     *
     * @return \DOMNodeList
     */
    public function query($xpath, \DOMNode $ctx = null) {
        if ($ctx === null) {
            $ctx = $this->getDom()->documentElement;
        }
        return $this->getXPath()->query($xpath, $ctx);
    }

    public function addElement(\DOMNode $node) {
        $this->getDom()->documentElement->appendChild($node);
    }

    public function save() {
        $this->getDom()->save($this->filename->asString());
    }

    /**
     * @return Directory
     */
    public function getDirectory() {
        return new Directory(dirname($this->filename->asString()));
    }

    private function init() {
        if ($this->dom instanceof \DOMDocument) {
            return;
        }

        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

        if ($this->filename->exists()) {
            $this->dom->load($this->filename->asString());
        } else {
            $this->dom->appendChild($this->dom->createElementNS($this->namespace, $this->rootElementName));
        }
        $this->xPath = new \DOMXPath($this->dom);
        $this->xPath->registerNamespace('phive', $this->namespace);
    }

    /**
     * @return \DOMDocument
     */
    public function getDom() {
        $this->init();
        return $this->dom;
    }

    /**
     * @return \DOMXPath
     */
    private function getXPath() {
        $this->init();
        return $this->xPath;
    }

}
