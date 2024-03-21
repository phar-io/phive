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

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\BaseHash
 * @covers \PharIo\Phive\Sha512Hash
 */
class Sha512HashTest extends TestCase {
    public static function invalidHashProvider() {
        return [
            ['foo'],
            ['123'],
            ['aa43f08c9402ca142f607fa2db0b1152cf248d49'],
            ['7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c'],
            ['98c11ffdfdd540676b1a137cb1a22b2a70350c9a44171d6b1180c6be5cbb2ee3f79d532c8a1dd9ef2e8e08e752a3babb']
        ];
    }

    public static function validHashProvider() {
        return [
            ['f7fbba6e0636f890e56fbbf3283e524c6fa3204ae298382d624741d0dc6638326e282c41be5e4254d8820772c5518a2c5a8c0c7f7eda19594a7eb539453e1ed7'],
            ['d82c4eb5261cb9c8aa9855edd67d1bd10482f41529858d925094d173fa662aa91ff39bc5b188615273484021dfb16fd8284cf684ccf0fc795be3aa2fc1e6c181']
        ];
    }

    /**
     * @dataProvider invalidHashProvider
     */
    public function testThrowsExceptionIfValueIsNotAValidSha512Hash($hashValue): void {
        $this->expectException(InvalidHashException::class);

        new Sha512Hash($hashValue);
    }

    /**
     * @dataProvider validHashProvider
     *
     * @param string $hashValue
     */
    public function testAsStringReturnsExpectedValue($hashValue): void {
        $hash = new Sha512Hash($hashValue);
        $this->assertSame($hashValue, $hash->asString());
    }

    public function testEquals(): void {
        $hash      = new Sha512Hash('f7fbba6e0636f890e56fbbf3283e524c6fa3204ae298382d624741d0dc6638326e282c41be5e4254d8820772c5518a2c5a8c0c7f7eda19594a7eb539453e1ed7');
        $otherHash = new Sha512Hash('f7fbba6e0636f890e56fbbf3283e524c6fa3204ae298382d624741d0dc6638326e282c41be5e4254d8820772c5518a2c5a8c0c7f7eda19594a7eb539453e1ed7');
        $this->assertTrue($hash->equals($otherHash));

        $hash      = new Sha512Hash('f7fbba6e0636f890e56fbbf3283e524c6fa3204ae298382d624741d0dc6638326e282c41be5e4254d8820772c5518a2c5a8c0c7f7eda19594a7eb539453e1ed7');
        $otherHash = new Sha512Hash('d82c4eb5261cb9c8aa9855edd67d1bd10482f41529858d925094d173fa662aa91ff39bc5b188615273484021dfb16fd8284cf684ccf0fc795be3aa2fc1e6c181');
        $this->assertFalse($hash->equals($otherHash));
    }

    public function testForContentCreatesExpectedHash(): void {
        $expected = new Sha512Hash('f7fbba6e0636f890e56fbbf3283e524c6fa3204ae298382d624741d0dc6638326e282c41be5e4254d8820772c5518a2c5a8c0c7f7eda19594a7eb539453e1ed7');
        $actual   = Sha512Hash::forContent('foo');

        $this->assertEquals($expected, $actual);
    }
}
