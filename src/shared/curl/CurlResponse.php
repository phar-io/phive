<?php
namespace PharIo\Phive {

    class CurlResponse {

        /**
         * @var string
         */
        private $responseBody = '';

        /**
         * @var array
         */
        private $curlInfo = [];

        /**
         * @var string
         */
        private $curlError = '';

        /**
         * @param string $responseBody
         * @param array  $curlInfo
         * @param string $curlError
         */
        public function __construct($responseBody, array $curlInfo, $curlError) {
            $this->responseBody = $responseBody;
            $this->curlInfo = $curlInfo;
            $this->curlError = $curlError;
        }

        /**
         * @return int
         */
        public function getHttpCode() {
            return $this->curlInfo['http_code'];
        }

        /**
         * @return bool
         */
        public function hasError() {
            return '' !== $this->curlError;
        }

        /**
         * @return string
         */
        public function getErrorMessage() {
            return $this->curlError;
        }

        /**
         * @return string
         */
        public function getBody() {
            return $this->responseBody;
        }

    }

}

