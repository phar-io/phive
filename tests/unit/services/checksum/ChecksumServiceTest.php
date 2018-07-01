<?php
namespace PharIo\Phive;

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

    /**
     * @expectedException \PharIo\Phive\InvalidHashException
     */
    public function testThrowsExceptionIfExpectedHashClassIsNotSupported() {
        $file = new File(new Filename('foo'), 'bar');
        $service = new ChecksumService();
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
    public function testVerifiesSha1Checksum($expectedHash, $actualHash, $expected) {
        $expectedHash = new Sha1Hash(hash('sha1', $expectedHash));

        /** @var File|ObjectProphecy $file */
        $file = $this->getFileProphecy();
        $file->getContent()->willReturn($actualHash);

        $service = new ChecksumService();
        $this->assertSame($expected, $service->verify($expectedHash, $file->reveal()));
    }

    /**
     * @return ObjectProphecy|File
     */
    private function getFileProphecy() {
        return $this->prophesize(File::class);
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
    public function testVerifiesSha256Checksum($expectedHash, $actualHash, $expected) {
        $expectedHash = new Sha256Hash(hash('sha256', $expectedHash));

        $file = $this->getFileProphecy();
        $file->getContent()->willReturn($actualHash);

        $service = new ChecksumService();
        $this->assertSame($expected, $service->verify($expectedHash, $file->reveal()));
    }

}
