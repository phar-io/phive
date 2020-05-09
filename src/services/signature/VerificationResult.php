<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface VerificationResult {
    public function getFingerprint(): string;

    public function isKnownKey(): bool;

    public function wasVerificationSuccessful(): bool;

    public function getStatusMessage(): string;
}
