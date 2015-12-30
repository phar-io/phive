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
         * @param Filename $filename
         * @param string $content
         */
        public function __construct(Filename $filename, $content) {
            $this->filename = $filename;
            $this->content = $content;
        }

        /**
         * @return Filename
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
         * @return Sha256Hash
         */
        public function getSha256Hash() {
            return new Sha256Hash(hash('sha256', $this->content));
        }

        /**
         * @param Filename $filename
         */
        public function saveAs(Filename $filename) {
            file_put_contents($filename->asString(), $this->getContent());
        }
    }

}
