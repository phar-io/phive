<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;

class Sha1Hash implements Hash {

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
     * @param string $content
     *
     * @return Hash
     */
    public static function forContent($content) {
        return new static(sha1($content));
    }

    /**
     * @param string $value
     *
     * @throws InvalidHashException
     */
    private function validateValue($value) {
        if (!preg_match('/^[0-9a-f]{40}$/i', $value)) {
            throw new InvalidHashException(sprintf('%s is not a valid SHA-1 hash', $value));
        }
    }

    /**
     * @param Hash $otherHash
     *
     * @return bool
     */
    public function equals(Hash $otherHash) {
        return $otherHash instanceof Sha1Hash && $otherHash->asString() === $this->asString();
    }

    /**
     * @return string
     */
    public function asString() {
        return $this->value;
    }

}
