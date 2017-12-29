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
     * @param Release $release
     *
     * @return Phar
     * @throws DownloadFailedException
     * @throws InvalidHashException
     * @throws VerificationFailedException
     */
    public function download(Release $release) {
        $pharFile = $this->downloadFile($release->getUrl());
        $signatureFile = $this->downloadFile($release->getSignatureUrl());

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
     * @param File  $phar
     * @param File  $signature
     * @param array $knownFingerprints
     *
     * @return VerificationResult
     */
    private function verifySignature(File $phar, File $signature, array $knownFingerprints) {
        return $this->signatureVerifier->verify($phar->getContent(), $signature->getContent(), $knownFingerprints);
    }

}
