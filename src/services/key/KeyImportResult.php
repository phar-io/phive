<?php declare(strict_types = 1);
namespace PharIo\Phive;

class KeyImportResult {

    /** @var int */
    private $count;

    /** @var string */
    private $fingerprint;

    public function __construct(int $count, string $fingerprint = '') {
        $this->count       = $count;
        $this->fingerprint = $fingerprint;
    }

    public function isSuccess(): bool {
        return $this->getCount() !== 0;
    }

    public function getCount(): int {
        return $this->count;
    }

    public function getFingerprint(): string {
        return $this->fingerprint;
    }
}
