<?php
namespace TheSeer\Phive {

    class PharFile {

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
    }

}
