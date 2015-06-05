<?php
namespace TheSeer\Phive {

    class GnupgVerificationResultTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider knownKeyProvider
         *
         * @param int $summary
         * @param bool $expected
         */
        public function testIsKnownKey($summary, $expected) {
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
         * @param int $summary
         * @param bool $expected
         */
        public function testWasVerificationSuccessful($summary, $expected) {
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

        public function testGetFingerprint() {
            $result = new GnupgVerificationResult(['summary' => 128, 'fingerprint' => 'foo']);
            $this->assertSame('foo', $result->getFingerprint());
        }

        /**
         * @dataProvider incompleteVerificationDataProvider
         * @expectedException \InvalidArgumentException
         *
         * @param array $verificationData
         */
        public function testThrowsExceptionIfVerificationDataIsIncomplete(array $verificationData) {
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

}

