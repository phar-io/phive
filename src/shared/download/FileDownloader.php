<?php
namespace PharIo\Phive {

    class FileDownloader {

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
         * @return File
         * @throws DownloadFailedException
         */
        public function download(Url $url) {
            $response = $this->curl->get($url);
            if ($response->getHttpCode() !== 200) {
                throw new DownloadFailedException(
                    sprintf(
                        'Download failed (HTTP status code %s) %s',
                        $response->getHttpCode(),
                        $response->getErrorMessage()
                    )
                );
            }
            return new File($this->getFilename($url), $response->getBody());
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

