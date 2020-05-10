<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\GnuPG\ErrorStrings;

class GnupgVerificationResult implements VerificationResult {

    /** @var array */
    private $verificationData;

    public function __construct(array $data) {
        $this->validate($data);
        $this->verificationData = $data;
    }

    public function getFingerprint(): string {
        return $this->verificationData['fingerprint'];
    }

    public function isKnownKey(): bool {
        return ($this->verificationData['summary'] & 128) !== 128;
    }

    public function wasVerificationSuccessful(): bool {
        return ($this->verificationData['summary'] === 0);
    }

    public function getErrorMessage(): string {
        return ErrorStrings::fromCode($this->verificationData['status']);
    }

    private function validate(array $keyinfo): void {
        if (!\array_key_exists('summary', $keyinfo) || !\array_key_exists('fingerprint', $keyinfo)) {
            throw new \InvalidArgumentException('Keyinfo does not contain required data');
        }
    }
}
