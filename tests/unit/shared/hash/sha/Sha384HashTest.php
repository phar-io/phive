<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Sha384Hash
 * @covers \PharIo\Phive\BaseHash
 */
class Sha384HashTest extends TestCase {

    public static function invalidHashProvider() {
        return [
            ['foo'],
            [123],
            ['aa43f08c9402ca142f607fa2db0b1152cf248d49'],
            ['7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c']
        ];
    }

    public static function validHashProvider() {
        return [
            ['98c11ffdfdd540676b1a137cb1a22b2a70350c9a44171d6b1180c6be5cbb2ee3f79d532c8a1dd9ef2e8e08e752a3babb'],
            ['14919aaff0da5efeb871fe8a438061c1996e88bfe199e2796b3b5c5c65714f61183adc53d48c3a32734ca6faf7d7fda8']
        ];
    }

    /**
     * @dataProvider invalidHashProvider
     *
     * @expectedException \PharIo\Phive\InvalidHashException
     *
     * @param mixed $hashValue
     */
    public function testThrowsExceptionIfValueIsNotAValidSha384Hash($hashValue) {
        new Sha384Hash($hashValue);
    }

    /**
     * @dataProvider validHashProvider
     *
     * @param string $hashValue
     */
    public function testAsStringReturnsExpectedValue($hashValue) {
        $hash = new Sha384Hash($hashValue);
        $this->assertSame($hashValue, $hash->asString());
    }

    public function testEquals() {
        $hash = new Sha384Hash('98c11ffdfdd540676b1a137cb1a22b2a70350c9a44171d6b1180c6be5cbb2ee3f79d532c8a1dd9ef2e8e08e752a3babb');
        $otherHash = new Sha384Hash('98c11ffdfdd540676b1a137cb1a22b2a70350c9a44171d6b1180c6be5cbb2ee3f79d532c8a1dd9ef2e8e08e752a3babb');
        $this->assertTrue($hash->equals($otherHash));

        $hash = new Sha384Hash('98c11ffdfdd540676b1a137cb1a22b2a70350c9a44171d6b1180c6be5cbb2ee3f79d532c8a1dd9ef2e8e08e752a3babb');
        $otherHash = new Sha384Hash('14919aaff0da5efeb871fe8a438061c1996e88bfe199e2796b3b5c5c65714f61183adc53d48c3a32734ca6faf7d7fda8');
        $this->assertFalse($hash->equals($otherHash));
    }

    public function testForContentCreatesExpectedHash() {
        $expected = new Sha384Hash('98c11ffdfdd540676b1a137cb1a22b2a70350c9a44171d6b1180c6be5cbb2ee3f79d532c8a1dd9ef2e8e08e752a3babb');
        $actual = Sha384Hash::forContent('foo');

        $this->assertEquals($expected, $actual);
    }
}




