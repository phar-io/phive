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
         * @var Input
         */
        private $input;

        /**
         * @param KeyDownloader $keyDownloader
         * @param KeyImporter   $keyImporter
         * @param Output        $output
         * @param Input         $input
         */
        public function __construct(
            KeyDownloader $keyDownloader,
            KeyImporter $keyImporter,
            Output $output,
            Input $input
        ) {
            $this->keyDownloader = $keyDownloader;
            $this->keyImporter = $keyImporter;
            $this->output = $output;
            $this->input = $input;
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
         * @throws VerificationFailedException
         */
        public function importKey($keyId, $key) {
            if (!$this->input->confirm(sprintf('Import key %s?', $keyId))) {
                throw new VerificationFailedException(sprintf('User declined import of key %s', $keyId));
            }

            return $this->keyImporter->importKey($key);
        }

    }

}

