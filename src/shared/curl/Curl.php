<?php
namespace TheSeer\Phive {

    class Curl {

        /**
         * @var CurlConfig
         */
        private $config;

        /**
         * @param CurlConfig $curlConfig
         */
        public function __construct(CurlConfig $curlConfig) {
            $this->config = $curlConfig;
        }

        /**
         * @param Url $url
         * @param array $params
         *
         * @return CurlResponse
         */
        public function get(Url $url, array $params = []) {
            $ch = curl_init($url . '?' . http_build_query($params));
            curl_setopt_array($ch, $this->config->asCurlOptArray());
            if ($this->config->hasLocalSslCertificate($url)) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->config->getLocalSslCertificate($url));
            }
            return new CurlResponse(curl_exec($ch), curl_getinfo($ch), curl_error($ch));
        }

    }

}

