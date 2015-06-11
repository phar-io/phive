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
         * @var PharService
         */
        private $pharService;

        /**
         * @var PharIoClient
         */
        private $pharIoClient;

        /**
         * @var LoggerInterface
         */
        private $logger;

        /**
         * @param PharService      $pharService
         * @param KeyService       $keyService
         * @param SignatureService $signatureService
         * @param LoggerInterface  $logger
         */
        public function __construct(
            PharService $pharService,
            KeyService $keyService,
            SignatureService $signatureService,
            LoggerInterface $logger
        ) {
            $this->pharService = $pharService;
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
            return $this->pharService->download($url);
        }

        /**
         * @param Url $url
         *
         * @return PharFile
         */
        public function downloadSignature(Url $url) {
            $this->logger->logInfo(sprintf('Downloading signature from %s', $url));
            return $this->pharService->download($url);
        }

        /**
         * @param PharFile $phar
         * @param bool     $makeCopy
         */
        public function installPhar(PharFile $phar, $makeCopy = false) {
            $this->logger->logInfo(sprintf('Installing PHAR %s', $phar->getFilename()));
            $this->pharService->install($phar, $makeCopy);
        }

    }

}
