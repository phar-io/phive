<?php
namespace TheSeer\Phive {

    class CurlConfig {

        /**
         * @var string optional proxy URL
         */
        private $proxyUrl;

        /**
         * @var string
         */
        private $proxyCredentials;

        /**
         * @var string
         */
        private $userAgent = '';

        /**
         * @param string $userAgent
         */
        public function __construct($userAgent) {
            $this->userAgent = $userAgent;
        }

        /**
         * @param string $url
         * @param string $username
         * @param string $password
         */
        public function setProxy($url, $username = '', $password = '') {
            $this->proxyUrl = $url;
            if ('' !== $username && '' !== $password) {
                $this->proxyCredentials = sprintf('%:%', $username, $password);
            }
        }

        /**
         * @return array
         */
        public function asCurlOptArray() {
            return [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_PROXY => $this->proxyUrl,
                CURLOPT_PROXYUSERPWD => $this->proxyCredentials
            ];
        }

    }

}

