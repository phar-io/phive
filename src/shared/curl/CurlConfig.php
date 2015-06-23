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
         * @param string $hostname
         * @param string $certFilename
         */
        public function addLocalSslCertificate($hostname, $certFilename) {
            $this->localSslCertificates[$hostname] = $certFilename;
        }

        /**
         * @param string $hostname
         *
         * @return bool
         *
         */
        public function hasLocalSslCertificate($hostname) {
            return array_key_exists($hostname, $this->localSslCertificates);
        }

        /**
         * @param string $hostname
         *
         * @return string
         * @throws CurlException
         */
        public function getLocalSslCertificate($hostname) {
            if (!$this->hasLocalSslCertificate($hostname)) {
                throw new CurlException(sprintf('No local certificate for hostname %s found', $hostname));
            }
            return $this->localSslCertificates[$hostname];
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

