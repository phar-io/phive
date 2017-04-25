<?php
namespace PharIo\Phive;

abstract class BaseHash implements Hash {

    /**
     * @var string
     */
    private $hash;

    /**
     * Hash constructor.
     *
     * @param string $hash
     */
    public function __construct($hash) {
        $this->ensureValidHash($hash);
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function asString() {
        return $this->hash;
    }

    /**
     * @param Hash $otherHash
     *
     * @return bool
     */
    public function equals(Hash $otherHash) {
        return hash_equals($this->hash, $otherHash->asString());
    }

    abstract protected function ensureValidHash($hash);
}
