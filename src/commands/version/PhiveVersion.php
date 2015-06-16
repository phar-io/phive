<?php
namespace TheSeer\Phive {

    class PhiveVersion {

        private $version;

        public function __construct($version = 'v0.1-dev') {
            $this->version = $version;
        }

        public function getVersion() {
            return $this->version;
        }

        public function getVersionString() {
            return 'Phive ' . $this->getVersion() . " - Copyright (C) 2015 by Arne Blankerts and contributors";
        }

    }

}
