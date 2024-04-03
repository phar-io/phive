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

use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\GnupgSignatureVerifier
 */
class GnupgSignatureVerifierTest extends TestCase {
    /** @var GnuPG|ObjectProphecy */
    private $gnupg;

    /** @var KeyService|ObjectProphecy */
    private $keyservice;

    protected function setUp(): void {
        $this->gnupg      = $this->prophesize(GnuPG::class);
        $this->keyservice = $this->prophesize(KeyService::class);
    }

    public function testThrowsVerificationFailedExceptionIfGnuPgThrowsException(): void {
        $this->gnupg->verify('foo', 'bar')->willThrow(new Exception());
        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal(), $this->keyservice->reveal());

        $this->expectException(VerificationFailedException::class);

        $verifier->verify('foo', 'bar', []);
    }

    public function testReturnsExpectedVerificationResult(): void {
        $verificationData = ['summary' => 1, 'fingerprint' => 'foo'];
        $this->gnupg->verify('foo', 'bar')->willReturn([$verificationData]);

        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal(), $this->keyservice->reveal());
        $actual   = $verifier->verify('foo', 'bar', []);
        $expected = new GnupgVerificationResult($verificationData);
        $this->assertEquals($expected, $actual);
    }

    public function testTriesToImportMissingKey(): void {
        $verificationData = ['summary' => 128, 'fingerprint' => 'foo'];
        $this->gnupg->verify('foo', 'bar')->willReturn([$verificationData]);
        $result = $this->prophesize(KeyImportResult::class);
        $result->isSuccess()->willReturn(true);

        $this->keyservice->importKey('foo', [])->shouldBeCalled()->willReturn($result->reveal());

        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal(), $this->keyservice->reveal());
        $verifier->verify('foo', 'bar', []);
    }
}
