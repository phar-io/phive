<?php
namespace shared\signature\gpg;

use Prophecy\Prophecy\ObjectProphecy;
use TheSeer\Phive\GnupgSignatureVerifier;
use TheSeer\Phive\GnupgVerificationResult;

class GnupgSignatureVerifierTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Gnupg|ObjectProphecy
     */
    private $gnupg;

    public function setUp() {
        $this->gnupg = $this->prophesize(\Gnupg::class);
    }

    /**
     * @expectedException \TheSeer\Phive\VerificationFailedException
     */
    public function testThrowsVerificationFailedExceptionIfGnuPgThrowsException() {
        $this->gnupg->verify('foo', 'bar')->willThrow(new \Exception());
        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal());
        $verifier->verify('foo', 'bar');
    }

    public function testReturnsExpectedVerificationResult() {
        $verificationData = ['summary' => 128, 'fingerprint' => 'foo'];
        $this->gnupg->verify('foo', 'bar')->willReturn([$verificationData]);
        $verifier = new GnupgSignatureVerifier($this->gnupg->reveal());
        $actual = $verifier->verify('foo', 'bar');
        $expected = new GnupgVerificationResult($verificationData);
        $this->assertEquals($expected, $actual);
    }

}
