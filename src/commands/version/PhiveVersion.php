<?php
namespace PharIo\Phive {

    class PhiveVersion {

        private $version;

        public function __construct($version = '0.1.0') {
            $this->version = $version;
        }

        public function getVersion() {
            return $this->version;
        }

        public function getVersionString() {
            return 'Phive ' . $this->getVersion() . " - Copyright (C) 2015 by Arne Blankerts and Sebastian Heuer";
        }

    }

}
