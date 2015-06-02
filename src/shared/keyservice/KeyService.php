<?php
namespace TheSeer\Phive {

    class KeyService {

        /**
         * @var KeyDownloaderInterface
         */
        private $keyDownloader;

        /**
         * @var KeyImporterInterface
         */
        private $keyImporter;

        /**
         * @var KeyRingInterface
         */
        private $keyRing;

        /**
         * @param KeyDownloaderInterface $keyDownloader
         * @param KeyImporterInterface   $keyImporter
         * @param KeyRingInterface       $keyRing
         */
        public function __construct(
            KeyDownloaderInterface $keyDownloader, KeyImporterInterface $keyImporter, KeyRingInterface $keyRing
        ) {
            $this->keyDownloader = $keyDownloader;
            $this->keyImporter = $keyImporter;
            $this->keyRing = $keyRing;
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

        /**
         * @param string $key
         *
         * @return bool
         */
        public function isKnownKey($key) {
            return $this->keyRing->hasKey($key);
        }

    }

}

