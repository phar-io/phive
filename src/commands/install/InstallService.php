<?php
namespace TheSeer\Phive {

    class InstallService  {

        /**
         * @var PharIoClient
         */
        private $pharIoClient;

        /**
         * @var PharDownloader
         */
        private $downloadClient;

        public function __construct(PharIoClient $pharIoClient, PharDownloader $downloadClient) {
            $this->pharIoClient = $pharIoClient;
            $this->downloadClient = $downloadClient;
        }

        /**
         * @param string $alias
         *
         * @return string
         */
        public function resolveAlias($alias) {
            return $this->pharIoClient->resolveAlias($alias);
        }

        /**
         * @param Url $url
         *
         * @return PharFile
         */
        public function downloadPhar(Url $url) {
            return $this->downloadClient->getFile($url);
        }

    }

}
