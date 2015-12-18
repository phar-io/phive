<?php
namespace PharIo\Phive {

    class File {

        /**
         * @var string
         */
        private $filename = '';

        /**
         * @var string
         */
        private $content = '';

        /**
         * @param string $filename
         * @param string $content
         */
        public function __construct($filename, $content) {
            $this->filename = $filename;
            $this->content = $content;
        }

        /**
         * @return string
         */
        public function getFilename() {
            return $this->filename;
        }

        /**
         * @return string
         */
        public function getContent() {
            return $this->content;
        }

        /**
         * @return Sha1Hash
         */
        public function getSha1Hash() {
            return new Sha1Hash(sha1($this->content));
        }

        /**
         * @param string $filename
         */
        public function saveAs($filename) {
            file_put_contents($filename, $this->getContent());
        }
    }

}
