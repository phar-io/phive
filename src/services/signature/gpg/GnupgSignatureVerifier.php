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
     * @param \Gnupg $gpg
     * @param KeyService $keyService
     */
    public function __construct(\Gnupg $gpg, KeyService $keyService) {
        $this->gpg = $gpg;
        $this->keyService = $keyService;
    }

    /**
     * @param string $message
     * @param string $signature
     * @param array $knownFingerprints
     *
     * @return GnupgVerificationResult
     * @throws VerificationFailedException
     */
    public function verify($message, $signature, array $knownFingerprints) {
        try {
            $result = new GnupgVerificationResult($this->gpg->verify($message, $signature)[0]);
            if (!$result->isKnownKey()) {
                $this->keyService->importKey($result->getFingerprint(), $knownFingerprints);
                $result = new GnupgVerificationResult($this->gpg->verify($message, $signature)[0]);
            }
            return $result;
        } catch (\Exception $e) {
            throw new VerificationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
