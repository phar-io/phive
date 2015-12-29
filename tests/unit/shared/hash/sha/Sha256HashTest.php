<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\Sha256Hash
     */
    class Sha256HashTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider invalidHashProvider
         *
         * @expectedException \PharIo\Phive\InvalidHashException
         *
         * @param mixed $hashValue
         */
        public function testThrowsExceptionIfValueIsNotAValidSha256Hash($hashValue)  {
            new Sha256Hash($hashValue);
        }

        /**
         * @dataProvider validHashProvider
         *
         * @param string $hashValue
         */
        public function testAsStringReturnsExpectedValue($hashValue) {
            $hash = new Sha256Hash($hashValue);
            $this->assertSame($hashValue, $hash->asString());
        }

        public static function invalidHashProvider() {
            return [
                ['foo'],
                [123],
                ['aa43f08c9402ca142f607fa2db0b1152cf248d49']
            ];
        }

        public static function validHashProvider() {
            return [
                ['7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c'],
                ['3060ec805184c4cc31c45a81d456f74dcca9ca05efa662442ef9bf74ffa86e7c']
            ];
        }
    }

}


