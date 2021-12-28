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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\GnupgVerificationResult
 */
class GnupgVerificationResultTest extends TestCase {
    /**
     * @dataProvider knownKeyProvider
     *
     * @param int  $summary
     * @param bool $expected
     */
    public function testIsKnownKey($summary, $expected): void {
        $result = new GnupgVerificationResult(['summary' => $summary, 'fingerprint' => 'foo']);
        $this->assertSame($expected, $result->isKnownKey());
    }

    public function knownKeyProvider() {
        return [
            [128, false],
            [0, true],
            [96, true],
            [136, false]
        ];
    }

    /**
     * @dataProvider wasVerificationSuccessfulProvider
     *
     * @param int  $summary
     * @param bool $expected
     */
    public function testWasVerificationSuccessful($summary, $expected): void {
        $result = new GnupgVerificationResult(['summary' => $summary, 'fingerprint' => 'foo']);
        $this->assertSame($expected, $result->wasVerificationSuccessful());
    }

    public function wasVerificationSuccessfulProvider() {
        return [
            [128, false],
            [0, true],
            [2, false],
            [136, false]
        ];
    }

    public function testGetFingerprint(): void {
        $result = new GnupgVerificationResult(['summary' => 128, 'fingerprint' => 'foo']);
        $this->assertSame('foo', $result->getFingerprint());
    }

    /**
     * @dataProvider incompleteVerificationDataProvider
     */
    public function testThrowsExceptionIfVerificationDataIsIncomplete(array $verificationData): void {
        $this->expectException(InvalidArgumentException::class);

        new GnupgVerificationResult($verificationData);
    }

    public function incompleteVerificationDataProvider() {
        return [
            [[]],
            [['summary' => 'foo']],
            [['fingerprint' => 'foo']]
        ];
    }
}
