<?php
namespace TheSeer\Phive {

    class KeyService {

        /**
         * @var PgpKeyDownloader
         */
        private $keyDownloader;

        /**
         * @var GnupgKeyImporter
         */
        private $keyImporter;

        /**
         * @param PgpKeyDownloader $keyDownloader
         * @param GnupgKeyImporter $keyImporter
         */
        public function __construct(PgpKeyDownloader $keyDownloader, GnupgKeyImporter $keyImporter) {
            $this->keyDownloader = $keyDownloader;
            $this->keyImporter = $keyImporter;
        }

        /**
         * @param string $keyId
         *
         * @return string
         */
        public function downloadKey($keyId) {
            return $this->keyDownloader->download($keyId);
        }

        /**
         * @param string
         *
         * @return mixed
         */
        public function importKey($key) {
            return $this->keyImporter->importKey($key);
        }

    }

}

