<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Sha256Hash
 * @covers \PharIo\Phive\BaseHash
 */
class Sha256HashTest extends TestCase {

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

    /**
     * @dataProvider invalidHashProvider
     *
     * @expectedException \PharIo\Phive\InvalidHashException
     *
     * @param mixed $hashValue
     */
    public function testThrowsExceptionIfValueIsNotAValidSha256Hash($hashValue) {
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

    public function testEquals() {
        $hash = new Sha256Hash('7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c');
        $otherHash = new Sha256Hash('7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c');
        $this->assertTrue($hash->equals($otherHash));

        $hash = new Sha256Hash('3060ec805184c4cc31c45a81d456f74dcca9ca05efa662442ef9bf74ffa86e7c');
        $otherHash = new Sha256Hash('7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c');
        $this->assertFalse($hash->equals($otherHash));
    }

    public function testForContentCreatesExpectedHash() {
        $expected = new Sha256Hash('2c26b46b68ffc68ff99b453c1d30413413422d706483bfa0f98a5e886266e7ae');
        $actual = Sha256Hash::forContent('foo');

        $this->assertEquals($expected, $actual);
    }
}
