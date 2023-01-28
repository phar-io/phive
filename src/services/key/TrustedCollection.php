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

use function in_array;

class TrustedCollection {
    /** @var string[] */
    private $keyIds = [];

    public function add(string $keyId): void {
        $this->keyIds[] = $keyId;
    }

    public function has(PublicKey $key): bool {
        return in_array($key->getId(), $this->keyIds, true) ||
               in_array($key->getFingerprint(), $this->keyIds, true);
    }
}
