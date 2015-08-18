<?php
namespace PharIo\Phive {

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
         * @param string    $filename
         */
        public function __construct($filename) {
            $this->filename = $filename;
            $this->init();
        }

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

        /**
         *
         */
        protected function save() {
            $this->dom->save($this->filename);
        }

        /**
         * @return string
         */
        abstract protected function getRootElementName();

        /**
         *
         */
        private function init() {
            $this->dom = new \DOMDocument('1.0', 'UTF-8');
            $this->dom->preserveWhiteSpace = false;
            $this->dom->formatOutput = true;
            if (file_exists($this->filename)) {
                $this->dom->load($this->filename);
            } else {
                $this->dom->appendChild($this->dom->createElement($this->getRootElementName()));
            }
            $this->xPath = new \DOMXPath($this->dom);
        }

    }

}

