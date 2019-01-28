<?php declare(strict_types = 1);
namespace PharIo\Phive;

class Sha1Hash extends BaseHash {
    public static function forContent(string $content): Hash {
        return new static(\hash('sha1', $content));
    }

    /**
     * @throws InvalidHashException
     */
    protected function ensureValidHash(string $hash): void {
        if (!\preg_match('/^[0-9a-f]{40}$/i', $hash)) {
            throw new InvalidHashException(\sprintf('%s is not a valid SHA-1 hash', $hash));
        }
    }
}
