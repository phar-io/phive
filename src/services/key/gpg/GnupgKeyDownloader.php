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
         * @var LoggerInterface
         */
        private $logger;

        /**
         * @param Curl            $curl
         * @param Url[]           $keyServers
         * @param LoggerInterface $logger
         */
        public function __construct(Curl $curl, array $keyServers, LoggerInterface $logger) {
            $this->curl = $curl;
            $this->keyServers = $keyServers;
            $this->logger = $logger;
        }

        /**
         * @param string $keyId
         *
         * @return string
         * @throws DownloadFailedException
         */
        public function download($keyId) {
            $params = [
                'search' => '0x' . $keyId,
                'op' => 'get',
                'options' => 'mr'
            ];
            foreach ($this->keyServers as $keyServer) {
                $this->logger->logInfo(sprintf('Trying %s', $keyServer));
                $result = $this->curl->get(new Url($keyServer . self::PATH), $params);
                if ($result->getHttpCode() == 200) {
                    $this->logger->logInfo('Sucessfully downloaded key');
                    return $result->getBody();
                }
                $this->logger->logWarning(
                    sprintf('Failed with status code %s: %s', $result->getHttpCode(), $result->getErrorMessage())
                );
            }
            throw new DownloadFailedException(sprintf('Key %s not found on key servers', $keyId));
        }

    }

}

