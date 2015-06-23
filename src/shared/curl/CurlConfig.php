<?php
namespace TheSeer\Phive {

    class CurlConfig {

        /**
         * @var string optional proxy URL
         */
        private $proxyUrl;

        /**
         * @var string optional proxy credentials
         */
        private $proxyCredentials;

        /**
         * @var string
         */
        private $userAgent = '';

        /**
         * @var array
         */
        private $localSslCertificates = [];

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
                $this->proxyCredentials = sprintf('%s:%s', $username, $password);
            }
        }

        /**
         * @param Url    $url
         * @param string $certFilename
         */
        public function addLocalSslCertificate(Url $url, $certFilename) {
            $this->localSslCertificates[parse_url($url, PHP_URL_HOST)] = $certFilename;
        }

        /**
         * @param Url $url
         *
         * @return bool
         */
        public function hasLocalSslCertificate(Url $url) {
            return array_key_exists(parse_url($url, PHP_URL_HOST), $this->localSslCertificates);
        }

        /**
         * @param Url $url
         *
         * @return string
         * @throws CurlException
         */
        public function getLocalSslCertificate(Url $url) {
            if (!$this->hasLocalSslCertificate($url)) {
                throw new CurlException(sprintf('No local certificate for %s found', (string)$url));
            }
            return $this->localSslCertificates[parse_url($url, PHP_URL_HOST)];
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

