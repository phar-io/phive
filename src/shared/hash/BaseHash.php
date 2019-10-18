<?php declare(strict_types = 1);
namespace PharIo\Phive;

abstract class BaseHash implements Hash {
    /** @var string */
    private $hash;

    public function __construct(string $hash) {
        $this->ensureValidHash($hash);
        $this->hash = $hash;
    }

    public function asString(): string {
        return $this->hash;
    }

    public function equals(Hash $otherHash): bool {
        return \hash_equals($this->hash, $otherHash->asString());
    }

    abstract protected function ensureValidHash(string $hash): void;
}
