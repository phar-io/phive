<?php
namespace PharIo\Phive;

abstract class XmlRepository {

    /**
     * @var string
     */
    private $filename = '';

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DOMXPath
     */
    private $xPath;

    /**
     * @param File $filename
     */
    public function __construct(Filename $filename) {
        $this->filename = $filename;
        $this->init();
    }

    /**
     *
     */
    private function init() {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        if ($this->filename->exists()) {
            $this->dom->load($this->filename->asString());
        } else {
            $this->dom->appendChild($this->dom->createElementNS($this->getNamespace(), $this->getRootElementName()));
        }
        $this->xPath = new \DOMXPath($this->dom);
        $this->xPath->registerNamespace('phive', $this->getNamespace());
    }

    /**
     * @return string
     */
    abstract protected function getNamespace();

    /**
     * @return string
     */
    abstract protected function getRootElementName();

    /**
     * @return \DOMDocument
     */
    protected function getDom() {
        return $this->dom;
    }

    /**
     * @return string
     */
    protected function getFilename() {
        return $this->filename;
    }

    /**
     * @return \DOMXPath
     */
    protected function getXPath() {
        return $this->xPath;
    }

}



