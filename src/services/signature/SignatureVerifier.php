<?php
namespace PharIo\Phive;

interface SignatureVerifier {

    /**
     * @param string $message
     * @param string $signature
     * @param array $knownFingerprints
     *
     * @return VerificationResult
     */
    public function verify($message, $signature, array $knownFingerprints);

}
