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

use function hash_equals;

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
        return hash_equals($this->hash, $otherHash->asString());
    }

    abstract protected function ensureValidHash(string $hash): void;
}
