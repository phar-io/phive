<?php
namespace PharIo\Phive {

    class PharDatabase {

        /**
         * @var string
         */
        private $filename = '';

        /**
         * @var Directory
         */
        private $pharDirectory;

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
         * @param Directory $pharDirectory
         */
        public function __construct($filename, Directory $pharDirectory) {
            $this->filename = $filename;
            $this->pharDirectory = $pharDirectory;
            $this->init();
        }

        /**
         * @param Phar $phar
         */
        public function addPhar(Phar $phar) {
            $this->savePhar($phar->getFile());
            $pharNode = $this->dom->createElement('phar');
            $pharNode->setAttribute('name', $phar->getName());
            $pharNode->setAttribute('version', $phar->getVersion()->getVersionString());
            $pharNode->setAttribute('location', $this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFile()->getFilename());
            $hashNode = $this->dom->createElement('hash', $phar->getFile()->getSha1Hash());
            $hashNode->setAttribute('type', 'sha1');
            $pharNode->appendChild($hashNode);
            $this->dom->firstChild->appendChild($pharNode);
            $this->save();
        }

        /**
         * @todo this belongs elsewhere
         *
         * @param File $pharFile
         */
        private function savePhar(File $pharFile) {
            $destination = $this->pharDirectory . DIRECTORY_SEPARATOR . $pharFile->getFilename();
            file_put_contents($destination, $pharFile->getContent());
            chmod($destination, 0755);
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
         * @param string  $name
         * @param Version $version
         *
         * @return Phar
         * @throws PharDatabaseException
         */
        public function getPhar($name, Version $version) {
            if (!$this->hasPhar($name, $version)) {
                throw new PharDatabaseException(sprintf('Phar %s %s not found in database.', $name, $version->getVersionString()));
            }
            return $this->nodetoPhar($this->getFirstMatchingPharNode($name, $version));
        }

        /**
         * @param string $filename
         *
         * @return Phar
         * @throws PharDatabaseException
         */
        public function getPharByUsage($filename) {
            $pharNode = $this->xPath->query(sprintf('//phar[usage/@destination="%s"]', $filename))->item(0);
            if (null === $pharNode) {
                throw new PharDatabaseException(sprintf('No phar with usage %s found', $filename));
            }
            /** @var \DOMElement $pharNode */
            return $this->nodetoPhar($pharNode);
        }

        /**
         * @param Phar   $phar
         * @param string $destination
         */
        public function addUsage(Phar $phar, $destination) {
            $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
            if ($this->xPath->query(sprintf('//usage[@destination="%s"]', $destination), $pharNode)->length !== 0) {
                return;
            }
            $usageNode = $this->dom->createElement('usage');
            $usageNode->setAttribute('destination', $destination);
            $pharNode->appendChild($usageNode);
            $this->save();
        }

        /**
         * @param Phar $phar
         *
         * @return bool
         */
        public function hasUsages(Phar $phar) {
            $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
            return $this->xPath->query('//usage', $pharNode)->length > 0;
        }

        /**
         * @param Phar   $phar
         * @param string $destination
         */
        public function removeUsage(Phar $phar, $destination) {
            $pharNode = $this->getFirstMatchingPharNode($phar->getName(), $phar->getVersion());
            $usageNode = $this->xPath->query(sprintf('//usage[@destination="%s"]', $destination), $pharNode)->item(0);
            $pharNode->removeChild($usageNode);
            $this->save();
        }

        /**
         * @param string $filename
         *
         * @return File
         */
        private function loadPharFile($filename) {
            return new File(pathinfo($filename, PATHINFO_BASENAME), file_get_contents($filename));
        }

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
                $this->dom->appendChild($this->dom->createElement('phars'));
            }
            $this->xPath = new \DOMXPath($this->dom);
        }

        /**
         *
         */
        private function save() {
            $this->dom->save($this->filename);
        }

        /**
         * @param string  $name
         * @param Version $version
         *
         * @return \DOMElement
         */
        private function getFirstMatchingPharNode($name, Version $version) {
            $query = sprintf('//phar[@name="%s" and @version="%s"]', $name, $version->getVersionString());
            return $this->xPath->query($query)->item(0);
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

    }

}

