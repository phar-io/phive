<?php
namespace PharIo\Phive {

    class PharDownloader {

        /**
         * @var Curl
         */
        private $curl;

        /**
         * @var SignatureService
         */
        private $signatureService;

        /**
         * @param Curl             $curl
         * @param SignatureService $signatureService
         */
        public function __construct(Curl $curl, SignatureService $signatureService) {
            $this->curl = $curl;
            $this->signatureService = $signatureService;
        }

        /**
         * @param Url $url
         *
         * @return File
         * @throws DownloadFailedException
         * @throws VerificationFailedException
         */
        public function download(Url $url) {
            $pharFile = $this->downloadFile($url);
            $signatureFile = $this->downloadFile($this->getSignatureUrl($url));
            if (!$this->verifySignature($pharFile, $signatureFile)) {
                throw new VerificationFailedException('Signature could not be verified');
            }
            return $pharFile;
        }

        /**
         * @param Url $url
         *
         * @return File
         * @throws DownloadFailedException
         */
        private function downloadFile(Url $url) {
            $result = $this->curl->get($url);
            if ($result->getHttpCode() !== 200) {
                throw new DownloadFailedException($result->getErrorMessage(), $result->getHttpCode());
            }
            return new File($this->getFilename($url), $result->getBody());
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

        /**
         * @param Url $url
         *
         * @return string
         */
        private function getFilename(Url $url) {
            return pathinfo($url, PATHINFO_BASENAME);
        }
    }

}
