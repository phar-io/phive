<?php
namespace TheSeer\Phive {

    class Phar {

        /**
         * @var string
         */
        private $name = '';

        /**
         * @var Version
         */
        private $version;

        /**
         * @var PharFile
         */
        private $file;

        /**
         * @param string   $name
         * @param Version  $version
         * @param PharFile $file
         */
        public function __construct($name, Version $version, PharFile $file) {
            $this->name = $name;
            $this->file = $file;
            $this->version = $version;
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @return Version
         */
        public function getVersion() {
            return $this->version;
        }

        /**
         * @return PharFile
         */
        public function getFile() {
            return $this->file;
        }

    }

}

