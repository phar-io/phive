<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
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
