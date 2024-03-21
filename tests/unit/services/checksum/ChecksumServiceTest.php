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

use function hash;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\ChecksumService
 */
class ChecksumServiceTest extends TestCase {
    public static function hashProvider() {
        return [
            ['foo', 'foo', true],
            ['foo', 'bar', false],
            ['bar', 'tool', false]
        ];
    }

    public function testThrowsExceptionIfExpectedHashClassIsNotSupported(): void {
        $file    = new File(new Filename('foo'), 'bar');
        $service = new ChecksumService();

        $this->expectException(InvalidHashException::class);

        $service->verify(new UnsupportedHashStub(), $file);
    }

    /**
     * @dataProvider hashProvider
     *
     * @param string $expectedHash
     * @param string $actualHash
     * @param bool   $expected
     *
     * @throws InvalidHashException
     */
    public function testVerifiesSha1Checksum($expectedHash, $actualHash, $expected): void {
        $expectedHash = new Sha1Hash(hash('sha1', $expectedHash));

        /** @var File|ObjectProphecy $file */
        $file = $this->getFileProphecy();
        $file->getContent()->willReturn($actualHash);

        $service = new ChecksumService();
        $this->assertSame($expected, $service->verify($expectedHash, $file->reveal()));
    }

    /**
     * @dataProvider hashProvider
     *
     * @param string $expectedHash
     * @param string $actualHash
     * @param bool   $expected
     *
     * @throws InvalidHashException
     */
    public function testVerifiesSha256Checksum($expectedHash, $actualHash, $expected): void {
        $expectedHash = new Sha256Hash(hash('sha256', $expectedHash));

        $file = $this->getFileProphecy();
        $file->getContent()->willReturn($actualHash);

        $service = new ChecksumService();
        $this->assertSame($expected, $service->verify($expectedHash, $file->reveal()));
    }

    /**
     * @return File|ObjectProphecy
     */
    private function getFileProphecy() {
        return $this->prophesize(File::class);
    }
}
