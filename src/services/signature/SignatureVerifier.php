<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface SignatureVerifier {
    public function verify(string $message, string $signature, array $knownFingerprints): VerificationResult;
}
