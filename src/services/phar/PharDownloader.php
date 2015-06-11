<?php
namespace TheSeer\Phive {

    class PharDownloader {

        /**
         * @var Curl
         */
        private $curl;

        /**
         * @param Curl $curl
         */
        public function __construct(Curl $curl) {
            $this->curl = $curl;
        }

        /**
         * @param Url $url
         *
         * @return PharFile
         * @throws DownloadFailedException
         */
        public function getFile(Url $url) {
            $result = $this->curl->get($url);
            if ($result->getHttpCode() == 200) {
                return new PharFile($this->getFilename($url), $result->getBody());
            }
            throw new DownloadFailedException($result->getErrorMessage(), $result->getHttpCode());
        }

        /**
         * @param Url $url
         *
         * @return string
         */
        private function getFilename(Url $url) {
            return pathinfo($url, PATHINFO_BASENAME);
        }
    }

}
