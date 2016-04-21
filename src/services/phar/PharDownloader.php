<?php
namespace PharIo\Phive;

class PharDownloader {

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @var SignatureVerifier
     */
    private $signatureVerifier;

    /**
     * @var ChecksumService
     */
    private $checksumService;

    /**
     * @var PharRegistry
     */
    private $pharRegistry;

    /**
     * @param FileDownloader $fileDownloader
     * @param SignatureVerifier $signatureVerifier
     * @param ChecksumService $checksumService
     * @param PharRegistry $pharRegistry
     */
    public function __construct(
        FileDownloader $fileDownloader,
        SignatureVerifier $signatureVerifier,
        ChecksumService $checksumService,
        PharRegistry $pharRegistry
    ) {
        $this->fileDownloader = $fileDownloader;
        $this->signatureVerifier = $signatureVerifier;
        $this->checksumService = $checksumService;
        $this->pharRegistry = $pharRegistry;
    }

    /**
     * @param Release $release
     *
     * @return Phar
     * @throws DownloadFailedException
     * @throws InvalidHashException
     * @throws VerificationFailedException
     */
    public function download(Release $release) {
        $pharFile = $this->fileDownloader->download($release->getUrl());
        $signatureFile = $this->fileDownloader->download($this->getSignatureUrl($release->getUrl()));
        $signatureVerificationResult = $this->verifySignature(
            $pharFile,
            $signatureFile,
            $this->pharRegistry->getKnownSignatureFingerprints($release->getName())
        );
        if (!$signatureVerificationResult->wasVerificationSuccessful()) {
            throw new VerificationFailedException('Signature could not be verified');
        }
        if ($release->hasExpectedHash() && !$this->checksumService->verify($release->getExpectedHash(), $pharFile)) {
            throw new VerificationFailedException(
                sprintf('Wrong checksum! Expected %s', $release->getExpectedHash()->asString())
            );
        }
        return new Phar($release->getName(), $release->getVersion(), $pharFile, $signatureVerificationResult->getFingerprint());
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
     * @param array $knownFingerprints
     *
     * @return VerificationResult
     */
    private function verifySignature(File $phar, File $signature, array $knownFingerprints) {
        return $this->signatureVerifier->verify($phar->getContent(), $signature->getContent(), $knownFingerprints);
    }

}
