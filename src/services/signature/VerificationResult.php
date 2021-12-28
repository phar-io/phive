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

interface VerificationResult {
    public function getFingerprint(): string;

    public function isKnownKey(): bool;

    public function wasVerificationSuccessful(): bool;

    public function getErrorMessage(): string;
}
