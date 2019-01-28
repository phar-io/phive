<?php declare(strict_types = 1);
namespace PharIo\Phive;

class KeyIdCollection {

    /** @var string[] */
    private $keyIds = [];

    public function addKeyId(string $keyId): void {
        $this->keyIds[] = $keyId;
    }

    public function has(string $keyId): bool {
        return \in_array($keyId, $this->keyIds, true);
    }
}
