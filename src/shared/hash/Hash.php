<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface Hash {
    public static function forContent(string $content): self;

    public function asString(): string;

    public function equals(self $otherHash): bool;
}
