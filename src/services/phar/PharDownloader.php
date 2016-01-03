<?php
namespace PharIo\Phive;

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
     * @var ChecksumService
     */
    private $checksumService;

    /**
     * @param FileDownloader   $fileDownloader
     * @param SignatureService $signatureService
     * @param ChecksumService  $checksumService
     */
    public function __construct(
        FileDownloader $fileDownloader, SignatureService $signatureService, ChecksumService $checksumService
    ) {
        $this->fileDownloader = $fileDownloader;
        $this->signatureService = $signatureService;
        $this->checksumService = $checksumService;
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
        if ($release->hasExpectedHash() && !$this->checksumService->verify($release->getExpectedHash(), $pharFile)) {
            throw new VerificationFailedException(
                sprintf('Wrong checksum! Expected %s', $release->getExpectedHash()->asString())
            );
        }
        return $pharFile;
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
     * @param File $phar
     * @param File $signature
     *
     * @return bool
     */
    private function verifySignature(File $phar, File $signature) {
        return $this->signatureService->verify($phar->getContent(), $signature->getContent());
    }

}
