<?php
namespace PharIo\Phive {

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
         * @var Output
         */
        private $output;

        /**
         * @param KeyDownloader $keyDownloader
         * @param KeyImporter   $keyImporter
         * @param Output        $output
         */
        public function __construct(
            KeyDownloader $keyDownloader,
            KeyImporter $keyImporter,
            Output $output
        ) {
            $this->keyDownloader = $keyDownloader;
            $this->keyImporter = $keyImporter;
            $this->output = $output;
        }

        /**
         * @param string $keyId
         *
         * @return string
         */
        public function downloadKey($keyId) {
            $this->output->writeInfo(sprintf('Downloading key %s', $keyId));
            return $this->keyDownloader->download($keyId);
        }

        /**
         * @param string
         *
         * @return mixed
         */
        public function importKey($key) {
            $this->output->writeInfo(sprintf('Importing key'));
            return $this->keyImporter->importKey($key);
        }

    }

}

