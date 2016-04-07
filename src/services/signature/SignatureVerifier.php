<?php
namespace PharIo\Phive;

interface SignatureVerifier {

    /**
     * @param string $message
     * @param string $signature
     *
     * @return VerificationResult
     */
    public function verify($message, $signature);

}
