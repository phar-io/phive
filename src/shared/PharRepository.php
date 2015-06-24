<?php
namespace TheSeer\Phive {

    class PharRepository {

        /**
         * @var PharDatabase
         */
        private $pharDatabase;

        /**
         * @var PharService
         */
        private $pharService;

        /**
         * @var Logger
         */
        private $logger;

        public function __construct(
            PharDatabase $pharDatabase,
            PharService $pharService,
            SignatureService $signatureService,
            KeyService $keyService,
            Logger $logger
        ) {
            $this->pharDatabase = $pharDatabase;
            $this->pharService = $pharService;
            $this->logger = $logger;
        }


        /**
         * @param Phar $phar
         * @param      $destination
         */
        public function addUsage(Phar $phar, $destination) {
            $this->pharDatabase->addUsage($phar, $destination);
        }

        /**
         * @param Url $url
         *
         * @return Phar
         * @throws VerificationFailedException
         */
        public function getByUrl(Url $url) {
            $name = $this->getPharName($url);
            $version = $this->getPharVersion($url);
            if ($this->pharDatabase->hasPhar($name, $version)) {
                $this->logger->logInfo(sprintf('Using existing copy of %s %s', $name, $version->getVersionString()));
                return $this->pharDatabase->getPhar($name, $version);
            }
            $this->logger->logInfo(sprintf('Downloading %s', $url));
            $pharFile = $this->pharService->download($url);
            $phar = new Phar($name, $version, $pharFile);
            $this->pharDatabase->addPhar($phar);
            return $phar;
        }

        /**
         * @param Url $url
         *
         * @return string
         */
        private function getPharName(Url $url) {
            $filename = pathinfo((string)$url, PATHINFO_FILENAME);
            preg_match('/(.*)-[0-9].[0-9].[0-9].*/',$filename, $matches);
            return $matches[1];
        }

        /**
         * @param URl $url
         *
         * @return Version
         */
        private function getPharVersion(URl $url) {
            $filename = pathinfo((string)$url, PATHINFO_FILENAME);
            preg_match('/-([0-9].[0-9].[0-9].*)/',$filename, $matches);
            return new Version($matches[1]);
        }


    }

}
