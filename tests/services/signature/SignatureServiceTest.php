<?php
namespace PharIo\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

    class SignatureServiceTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var SignatureVerifier|ObjectProphecy
         */
        private $verifier;

        public function setUp() {
            $this->verifier = $this->prophesize(SignatureVerifier::class);
        }

        public function testInvokesSignatureVerifier() {
            $expected = new GnupgVerificationResult(['fingerprint' => 'foobar', 'summary' => 'baz']);

            $this->verifier->verify('foo', 'bar')->willReturn($expected);

            $service = new SignatureService($this->verifier->reveal());
            $this->assertEquals($expected, $service->verify('foo', 'bar'));
        }

    }


}

