<?php
namespace TheSeer\Phive {

    class Curl {

        /**
         * @var string
         */
        private $httpsProxy = '';

        /**
         * @param string $httpsProxy
         */
        public function __construct($httpsProxy = '') {
            $this->httpsProxy = $httpsProxy;
        }

        /**
         * @param Url $url
         * @param array $params
         *
         * @return CurlResponse
         */
        public function get(Url $url, array $params = []) {
            $url .= '?' . http_build_query($params);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            if ('' !== $this->httpsProxy) {
                curl_setopt($ch, CURLOPT_PROXY, $this->httpsProxy);
            }
            return new CurlResponse(curl_exec($ch), curl_getinfo($ch), curl_error($ch));
        }

    }

}

