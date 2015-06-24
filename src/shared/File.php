<?php
namespace TheSeer\Phive {

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
         * @return string
         */
        public function getSha1Hash() {
            return sha1($this->content);
        }
    }

}
