<?php
namespace PharIo\Phive {

    class Release {

        /**
         * @var Version
         */
        private $version;

        /**
         * @var Url
         */
        private $url;

        /**
         * @var Hash
         */
        private $expectedHash;

        /**
         * @param Version $version
         * @param Url     $url
         * @param Hash    $expectedHash
         */
        public function __construct(Version $version, Url $url, Hash $expectedHash = null) {
            $this->version = $version;
            $this->url = $url;
            $this->expectedHash = $expectedHash;
        }

        /**
         * @return Version
         */
        public function getVersion() {
            return $this->version;
        }

        /**
         * @return Url
         */
        public function getUrl() {
            return $this->url;
        }

        /**
         * @return Hash
         */
        public function getExpectedHash() {
            return $this->expectedHash;
        }

        /**
         * @return bool
         */
        public function hasExpectedHash() {
            return null !== $this->expectedHash;
        }

    }

}

