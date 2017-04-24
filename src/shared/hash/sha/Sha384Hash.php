<?php
namespace PharIo\Phive;

class Sha384Hash extends BaseHash {

    /**
     * @param string $content
     *
     * @return Hash
     */
    public static function forContent($content) {
        return new static(hash('sha384', $content));
    }

    /**
     * @param string $hash
     *
     * @throws InvalidHashException
     */
    protected function ensureValidHash($hash) {
        if (!preg_match('/^[0-9a-f]{96}$/i', $hash)) {
            throw new InvalidHashException(sprintf('%s is not a valid SHA-385 hash', $hash));
        }
    }

}
