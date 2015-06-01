<?php
namespace TheSeer\Phive {

    class Curl {

        public function get($url, array $params = []) {
            $url .= '?' . http_build_query($params);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            $curlInfo = curl_getinfo($ch);
            if ($curlInfo['http_code'] !== 200) {
                throw new CurlException(curl_error($ch));
            }
            return $result;
        }

    }

}

