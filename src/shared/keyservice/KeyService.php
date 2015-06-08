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
         * @var LoggerInterface
         */
        private $logger;

        /**
         * @param KeyDownloaderInterface $keyDownloader
         * @param KeyImporterInterface   $keyImporter
         * @param KeyRingInterface       $keyRing
         * @param LoggerInterface        $logger
         */
        public function __construct(
            KeyDownloaderInterface $keyDownloader,
            KeyImporterInterface $keyImporter,
            KeyRingInterface $keyRing,
            LoggerInterface $logger
        ) {
            $this->keyDownloader = $keyDownloader;
            $this->keyImporter = $keyImporter;
            $this->keyRing = $keyRing;
            $this->logger = $logger;
        }

        /**
         * @param string $keyId
         *
         * @return string
         */
        public function downloadKey($keyId) {
            $this->logger->logInfo(sprintf('Downloading key %s', $keyId));
            return $this->keyDownloader->download($keyId);
        }

        /**
         * @param string
         *
         * @return mixed
         */
        public function importKey($key) {
            $this->logger->logInfo(sprintf('Importing key'));
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

