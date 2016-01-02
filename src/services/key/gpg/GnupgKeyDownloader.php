<?php
namespace PharIo\Phive {

    class GnupgKeyDownloader implements KeyDownloader {

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
         * @var Output
         */
        private $output;

        /**
         * @param Curl   $curl
         * @param Url[]  $keyServers
         * @param Output $output
         */
        public function __construct(Curl $curl, array $keyServers, Output $output) {
            $this->curl = $curl;
            $this->keyServers = $keyServers;
            $this->output = $output;
        }

        /**
         * @param string $keyId
         *
         * @return string
         * @throws DownloadFailedException
         */
        public function download($keyId) {
            $params = [
                'search'  => '0x' . $keyId,
                'op'      => 'get',
                'options' => 'mr'
            ];
            foreach ($this->keyServers as $keyServer) {
                $this->output->writeInfo(sprintf('Trying %s', $keyServer));
                $result = $this->curl->get(new Url($keyServer . self::PATH), $params);
                if ($result->getHttpCode() == 200) {
                    $this->output->writeInfo('Sucessfully downloaded key');
                    return $result->getBody();
                }
                $this->output->writeWarning(
                    sprintf('Failed with status code %s: %s', $result->getHttpCode(), $result->getErrorMessage())
                );
            }
            throw new DownloadFailedException(sprintf('Key %s not found on key servers', $keyId));
        }

    }

}

