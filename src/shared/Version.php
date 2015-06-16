<?php
namespace TheSeer\Phive {

    class Version {

        /**
         * @var string
         */
        private $major = '';

        /**
         * @var string
         */
        private $minor = '';

        /**
         * @var string
         */
        private $patch = '';

        /**
         * @var string
         */
        private $label = '';

        /**
         * @var string
         */
        private $buildMetaData = '';

        /**
         * @var string
         */
        private $versionString = '';

        /**
         * @param string $versionString
         */
        public function __construct($versionString) {
            $this->versionString = $versionString;
            $this->parseVersion($versionString);
        }

        /**
         * @param $versionString
         */
        private function parseVersion($versionString) {
            $this->extractBuildMetaData($versionString);
            $this->extractLabel($versionString);
            $versionSegments = explode('.', $versionString);
            $this->major = $versionSegments[0];
            $this->minor = $versionSegments[1];
            $this->patch = $versionSegments[2];
        }

        /**
         * @param string $versionString
         */
        private function extractBuildMetaData(&$versionString) {
            if (preg_match('/\+(.*)/', $versionString, $matches) == 1) {
                $this->buildMetaData = $matches[1];
                $versionString = str_replace($matches[0], '', $versionString);
            }
        }

        /**
         * @param string $versionString
         */
        private function extractLabel(&$versionString) {
            if (preg_match('/\-(.*)/', $versionString, $matches) == 1) {
                $this->label = $matches[1];
                $versionString = str_replace($matches[0], '', $versionString);
            }
        }

        /**
         * @return string
         */
        public function getMajor() {
            return $this->major;
        }

        /**
         * @return string
         */
        public function getMinor() {
            return $this->minor;
        }

        /**
         * @return string
         */
        public function getPatch() {
            return $this->patch;
        }

        /**
         * @return string
         */
        public function getLabel() {
            return $this->label;
        }

        /**
         * @return string
         */
        public function getBuildMetaData() {
            return $this->buildMetaData;
        }

        /**
         * @return string
         */
        public function getVersionString() {
            return $this->versionString;
        }

    }

}

