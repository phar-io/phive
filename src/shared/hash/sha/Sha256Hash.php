<?php
namespace PharIo\Phive;

class Sha256Hash implements Hash {

    /**
     * @var string
     */
    private $value = '';

    /**
     * @param string $value
     */
    public function __construct($value) {
        $this->validateValue($value);
        $this->value = $value;
    }

    /**
     * @param string $value
     *
     * @throws InvalidHashException
     */
    private function validateValue($value) {
        if (!preg_match('/^[0-9a-f]{64}$/i', $value)) {
            throw new InvalidHashException(sprintf('%s is not a valid SHA-256 hash', $value));
        }
    }

    /**
     * @param Hash $otherHash
     *
     * @return bool
     */
    public function equals(Hash $otherHash) {
        return $otherHash instanceof Sha256Hash && $otherHash->asString() == $this->asString();
    }

    /**
     * @return string
     */
    public function asString() {
        return $this->value;
    }

}



