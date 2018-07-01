<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\GnupgSignatureVerifier
 */
class GnupgSignatureVerifierTest extends TestCase {

    /**
     * @var \Gnupg|ObjectProphecy
     */
    private $gnupg;

    /**
     * @var KeyService|ObjectProphecy
     */
    private $keyservice;

    public function setUp() {
        $this->gnupg = $this->prophesize(\Gnupg::class);
        $this->keyservice = $this->prophesize(KeyService::class);
    }

    /**
     * @expectedException \PharIo\Phive\VerificationFailedException
     */
    public function testThrowsVerificationFailedExceptionIfGnuPgThrowsException() {
        $this->gnupg->verify('foo', 'bar')->willThrow(new \Exception());
        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal(), $this->keyservice->reveal());
        $verifier->verify('foo', 'bar', []);
    }

    public function testReturnsExpectedVerificationResult() {
        $verificationData = ['summary' => 1, 'fingerprint' => 'foo'];
        $this->gnupg->verify('foo', 'bar')->willReturn([$verificationData]);

        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal(), $this->keyservice->reveal());
        $actual = $verifier->verify('foo', 'bar', []);
        $expected = new GnupgVerificationResult($verificationData);
        $this->assertEquals($expected, $actual);
    }

    public function testTriesToImportMissingKey() {
        $verificationData = ['summary' => 128, 'fingerprint' => 'foo'];
        $this->gnupg->verify('foo', 'bar')->willReturn([$verificationData]);
        $result = $this->prophesize(KeyImportResult::class);
        $result->isSuccess()->willReturn(true);

        $this->keyservice->importKey('foo', [])->shouldBeCalled()->willReturn($result->reveal());

        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal(), $this->keyservice->reveal());
        $verifier->verify('foo', 'bar', []);
    }

}
