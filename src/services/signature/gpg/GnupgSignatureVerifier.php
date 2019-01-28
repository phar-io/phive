<?php declare(strict_types = 1);
namespace PharIo\Phive;

/**
 * GPG Signature Verification using the GnuPG PECL Extension.
 */
class GnupgSignatureVerifier implements SignatureVerifier {
    /** @var \Gnupg */
    private $gpg;

    /** @var KeyService */
    private $keyService;

    public function __construct(\Gnupg $gpg, KeyService $keyService) {
        $this->gpg        = $gpg;
        $this->keyService = $keyService;
    }

    /**
     * @throws VerificationFailedException
     */
    public function verify(string $message, string $signature, array $knownFingerprints): VerificationResult {
        try {
            $result = $this->attemptVerification($message, $signature);

            if (!$result->isKnownKey()) {
                $importResult = $this->keyService->importKey($result->getFingerprint(), $knownFingerprints);

                if (!$importResult->isSuccess()) {
                    return $result;
                }

                return $this->attemptVerification($message, $signature);
            }

            return $result;
        } catch (\Exception $e) {
            throw new VerificationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function attemptVerification(string $message, string $signature): GnupgVerificationResult {
        return new GnupgVerificationResult($this->gpg->verify($message, $signature)[0]);
    }
}
