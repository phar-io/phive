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
         * @param Version $version
         * @param Url     $url
         */
        public function __construct(Version $version, Url $url) {
            $this->version = $version;
            $this->url = $url;
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

    }

}

