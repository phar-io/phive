<?php
namespace TheSeer\Phive {

    class GnupgKeyDownloader implements KeyDownloaderInterface {

        const PATH = '/pks/lookup';

        /**
         * @var Curl
         */
        private $curl;

        /**
         * @var Url[]
         */
        private $keyServers = [];

        /**
         * @param Curl  $curl
         * @param Url[] $keyServers
         */
        public function __construct(Curl $curl, array $keyServers) {
            $this->curl = $curl;
            $this->keyServers = $keyServers;
        }

        /**
         * @param string $keyId
         *
         * @return string
         */
        public function download($keyId) {
            $params = [
                'search' => '0x' . $keyId,
                'op' => 'get',
                'options' => 'mr'
            ];
            foreach ($this->keyServers as $keyServer) {
                try {
                    $result = $this->curl->get($keyServer . self::PATH, $params);
                } catch (CurlException $e) {
                    continue;
                }
                return $result;
            }
            throw new \InvalidArgumentException(sprintf('Key %s not found on key servers', $keyId));
        }

    }

}

