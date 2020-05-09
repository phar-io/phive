<?php declare(strict_types = 1);
namespace PharIo\Phive;

class UnsupportedHashStub implements Hash {
    public static function forContent(string $content): Hash {
        return new static();
    }

    public function asString(): string {
        return 'foo';
    }

    public function equals(Hash $otherHash): bool {
        return false;
    }
}
