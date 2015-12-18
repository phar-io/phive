<?php
namespace PharIo\Phive {

    class PharDownloader {

        /**
         * @var FileDownloader
         */
        private $fileDownloader;

        /**
         * @var SignatureService
         */
        private $signatureService;

        /**
         * @param FileDownloader   $fileDownloader
         * @param SignatureService $signatureService
         */
        public function __construct(FileDownloader $fileDownloader, SignatureService $signatureService) {
            $this->fileDownloader = $fileDownloader;
            $this->signatureService = $signatureService;
        }

        /**
         * @param Release $release
         *
         * @return File
         * @throws DownloadFailedException
         * @throws VerificationFailedException
         */
        public function download(Release $release) {
            $pharFile = $this->fileDownloader->download($release->getUrl());
            $signatureFile = $this->fileDownloader->download($this->getSignatureUrl($release->getUrl()));
            if (!$this->verifySignature($pharFile, $signatureFile)) {
                throw new VerificationFailedException('Signature could not be verified');
            }
            return $pharFile;
        }

        /**
         * @param File $phar
         * @param File $signature
         *
         * @return bool
         */
        private function verifySignature(File $phar, File $signature) {
            return $this->signatureService->verify($phar->getContent(), $signature->getContent());
        }

        /**
         * @param Url $pharUrl
         *
         * @return Url
         */
        private function getSignatureUrl(Url $pharUrl) {
            return new Url($pharUrl . '.asc');
        }

    }

}
