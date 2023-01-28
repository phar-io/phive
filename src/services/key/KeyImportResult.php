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

class KeyImportResult {
    /** @var int */
    private $count;

    /** @var string */
    private $fingerprint;

    public function __construct(int $count, string $fingerprint = '') {
        $this->count       = $count;
        $this->fingerprint = $fingerprint;
    }

    public function isSuccess(): bool {
        return $this->getCount() !== 0;
    }

    public function getCount(): int {
        return $this->count;
    }

    public function getFingerprint(): string {
        return $this->fingerprint;
    }
}
