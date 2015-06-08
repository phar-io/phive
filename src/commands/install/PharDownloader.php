<?php
namespace TheSeer\Phive {

    class PharDownloader {

        /**
         * @param string $url
         *
         * @return PharFile
         */
        public function getFile($url) {
            $contents = file_get_contents($url);
            return new PharFile($this->getFilename($url), $contents);
        }

        /**
         * @param string $url
         *
         * @return string
         */
        private function getFilename($url) {
            return pathinfo($url, PATHINFO_BASENAME);
        }
    }

}
