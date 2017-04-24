<?php
namespace PharIo\Phive;

class Sha512Hash extends BaseHash {

    /**
     * @param string $content
     *
     * @return Hash
     */
    public static function forContent($content) {
        return new static(hash('sha512', $content));
    }

    /**
     * @param string $hash
     *
     * @throws InvalidHashException
     */
    protected function ensureValidHash($hash) {
        if (!preg_match('/^[0-9a-f]{128}$/i', $hash)) {
            throw new InvalidHashException(sprintf('%s is not a valid SHA-512 hash', $hash));
        }
    }

}
