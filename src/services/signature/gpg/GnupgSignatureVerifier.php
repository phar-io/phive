<?php
namespace PharIo\Phive;

/**
 * GPG Signature Verification using the GnuPG PECL Extension.
 */
class GnupgSignatureVerifier implements SignatureVerifier {

    /**
     * @var \Gnupg
     */
    private $gpg;

    /**
     * @var KeyService
     */
    private $keyService;

    /**
     * @param \Gnupg     $gpg
     * @param KeyService $keyService
     */
    public function __construct(\Gnupg $gpg, KeyService $keyService) {
        $this->gpg = $gpg;
        $this->keyService = $keyService;
    }

    /**
     * @param string $message
     * @param string $signature
     *
     * @return GnupgVerificationResult
     * @throws VerificationFailedException
     */
    public function verify($message, $signature) {
        try {
            $result = new GnupgVerificationResult($this->gpg->verify($message, $signature)[0]);
            if (!$result->wasVerificationSuccessful() && !$result->isKnownKey()) {
                $this->keyService->importKey(
                    $this->keyService->downloadKey($result->getFingerprint())
                );
            }
            return new GnupgVerificationResult($this->gpg->verify($message, $signature)[0]);
        } catch (\Exception $e) {
            throw new VerificationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
