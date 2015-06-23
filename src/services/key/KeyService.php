<?php
namespace TheSeer\Phive {

    class KeyService {

        /**
         * @var KeyDownloader
         */
        private $keyDownloader;

        /**
         * @var KeyImporter
         */
        private $keyImporter;

        /**
         * @var Logger
         */
        private $logger;

        /**
         * @param KeyDownloader $keyDownloader
         * @param KeyImporter   $keyImporter
         * @param Logger        $logger
         */
        public function __construct(
            KeyDownloader $keyDownloader,
            KeyImporter $keyImporter,
            Logger $logger
        ) {
            $this->keyDownloader = $keyDownloader;
            $this->keyImporter = $keyImporter;
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

    }

}

