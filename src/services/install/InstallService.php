<?php
namespace TheSeer\Phive {

    class InstallService {

        /**
         * @var KeyService
         */
        private $keyService;

        /**
         * @var SignatureService
         */
        private $signatureService;

        /**
         * @var PharIoClient
         */
        private $pharIoClient;

        /**
         * @var PharInstaller
         */
        private $pharInstaller;

        /**
         * @var PharDownloader
         */
        private $downloadClient;

        /**
         * @var LoggerInterface
         */
        private $logger;

        /**
         * @param PharIoClient     $pharIoClient
         * @param PharDownloader   $downloadClient
         * @param PharInstaller    $pharInstaller
         * @param KeyService       $keyService
         * @param SignatureService $signatureService
         * @param LoggerInterface  $logger
         */
        public function __construct(
            PharIoClient $pharIoClient,
            PharDownloader $downloadClient,
            PharInstaller $pharInstaller,
            KeyService $keyService,
            SignatureService $signatureService,
            LoggerInterface $logger
        ) {
            $this->pharIoClient = $pharIoClient;
            $this->downloadClient = $downloadClient;
            $this->pharInstaller = $pharInstaller;
            $this->keyService = $keyService;
            $this->signatureService = $signatureService;
            $this->logger = $logger;
        }

        /**
         * @param PharFile $phar
         * @param PharFile $signature
         *
         * @return bool
         */
        public function verifySignature(PharFile $phar, PharFile $signature) {
            $result = $this->verify($phar, $signature);
            if (!$result->wasVerificationSuccessful() && !$result->isKnownKey()) {
                $this->keyService->importKey($this->keyService->downloadKey($result->getFingerprint()));
                $result = $this->verify($phar, $signature);
            }
            return $result->wasVerificationSuccessful();
        }

        /**
         * @param PharFile $phar
         * @param PharFile $signature
         *
         * @return GnupgVerificationResult
         */
        private function verify(PharFile $phar, PharFile $signature) {
            $this->logger->logInfo(sprintf('Verifying signature %s', $signature->getFilename()));
            return $this->signatureService->verify($phar->getContent(), $signature->getContent());
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
            $this->logger->logInfo(sprintf('Downloading PHAR from %s', $url));
            return $this->downloadClient->getFile($url);
        }

        /**
         * @param Url $url
         *
         * @return PharFile
         */
        public function downloadSignature(Url $url) {
            $this->logger->logInfo(sprintf('Downloading signature from %s', $url));
            return $this->downloadClient->getFile($url);
        }

        /**
         * @param PharFile $phar
         * @param bool     $copy
         */
        public function installPhar(PharFile $phar, $copy = false) {
            $this->logger->logInfo(sprintf('Installing PHAR %s', $phar->getFilename()));
            $this->pharInstaller->install($phar, $copy);
        }

    }

}
