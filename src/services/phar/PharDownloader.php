<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;

class PharDownloader {

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
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @param HttpClient        $httpClient
     * @param SignatureVerifier $signatureVerifier
     * @param ChecksumService   $checksumService
     * @param PharRegistry      $pharRegistry
     */
    public function __construct(
        HttpClient $httpClient,
        SignatureVerifier $signatureVerifier,
        ChecksumService $checksumService,
        PharRegistry $pharRegistry
    ) {
        $this->signatureVerifier = $signatureVerifier;
        $this->checksumService = $checksumService;
        $this->pharRegistry = $pharRegistry;
        $this->httpClient = $httpClient;
    }

    /**
     * @param SupportedRelease $release
     *
     * @return Phar
     * @throws DownloadFailedException
     * @throws InvalidHashException
     */
    public function download(SupportedRelease $release) {
        $pharFile = $this->downloadFile($release->getUrl());

        $fingerprint = null;
        if ($release->hasSignatureUrl()) {
            $fingerprint = $this->verifySignature(
                $release,
                $pharFile,
                $this->pharRegistry->getKnownSignatureFingerprints($release->getName())
            );
        }

        return new Phar($release->getName(), $release->getVersion(), $pharFile, $fingerprint);
    }

    /**
     * @param Url $url
     *
     * @return File
     * @throws DownloadFailedException
     */
    private function downloadFile(Url $url) {
        try {
            $response = $this->httpClient->get($url);
            if (!$response->isSuccess()) {
                throw new DownloadFailedException(
                    sprintf('Failed to download load %s: HTTP Code %d', $url, $response->getHttpCode()),
                    $response->getHttpCode()
                );
            }

            return new File($url->getFilename(), $response->getBody());
        } catch (HttpException $e) {
            throw new DownloadFailedException(
                sprintf('Unexpected HTTP error when requesting %s: %s', $url, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param SupportedRelease $release
     * @param File             $phar
     * @param array            $knownFingerprints
     *
     * @return string
     *
     * @throws VerificationFailedException
     */
    private function verifySignature(SupportedRelease $release, File $phar, array $knownFingerprints) {
        if (!$release->hasSignatureUrl()) {
            return '{NONE}';
        }

        $signatureFile = $this->downloadFile($release->getSignatureUrl());
        $signatureVerificationResult = $this->signatureVerifier->verify($phar->getContent(), $signatureFile->getContent(), $knownFingerprints);

        if (!$signatureVerificationResult->wasVerificationSuccessful()) {
            throw new VerificationFailedException('Signature could not be verified');
        }
        if ($release->hasExpectedHash() && !$this->checksumService->verify($release->getExpectedHash(), $pharFile)) {
            throw new VerificationFailedException(
                sprintf('Wrong checksum! Expected %s', $release->getExpectedHash()->asString())
            );
        }

        return $signatureVerificationResult->getFingerprint();
    }

}
