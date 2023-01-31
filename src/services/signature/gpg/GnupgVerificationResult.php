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

use function array_key_exists;
use InvalidArgumentException;
use PharIo\GnuPG\ErrorStrings;

class GnupgVerificationResult implements VerificationResult {
    /** @var array */
    private $verificationData;

    public function __construct(array $data) {
        $this->validate($data);
        $this->verificationData = $data;
    }

    public function getFingerprint(): string {
        return $this->verificationData['fingerprint'];
    }

    public function isKnownKey(): bool {
        return ($this->verificationData['summary'] & 128) !== 128;
    }

    public function wasVerificationSuccessful(): bool {
        return $this->verificationData['summary'] === 0;
    }

    public function getErrorMessage(): string {
        return ErrorStrings::fromCode($this->verificationData['status']);
    }

    private function validate(array $keyinfo): void {
        if (!array_key_exists('summary', $keyinfo) || !array_key_exists('fingerprint', $keyinfo)) {
            throw new InvalidArgumentException('Keyinfo does not contain required data');
        }
    }
}
