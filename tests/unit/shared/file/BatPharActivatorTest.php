<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\BatPharActivator
 */
class BatPharActivatorTest extends TestCase {

    public function testCreatesExpectedBatFile() {
        $activator = new BatPharActivator('foo ##PHAR_FILENAME## bar');
        $createdFile = $activator->activate(new Filename('some.phar'), new Filename(__DIR__ . '/some'));

        $this->assertEquals('foo some.phar bar', file_get_contents($createdFile));
    }

    public function testCreatesExpectedBatFileWithPath() {
        $activator = new BatPharActivator('foo ##PHAR_FILENAME## bar');
        $createdFile = $activator->activate(
           new Filename(__DIR__ . '/path/some.phar'),
           new Filename(__DIR__ . '/some')
        );

        $this->assertEquals('foo ' . __DIR__ . '/path/some.phar bar', file_get_contents($createdFile));
    }

    public function testCreatesExpectedBatFileWithPathPlaceholder() {
        $activator = new BatPharActivator('foo ##PHAR_FILENAME## bar');
        $createdFile = $activator->activate(
           new Filename(__DIR__ . '/some.phar'),
           new Filename(__DIR__ . '/some')
        );

        $this->assertEquals('foo %~dp0some.phar bar', file_get_contents($createdFile));
    }

    public function testDestinationNotWritableExpectingException() {
        $directory = $this->createMock(Directory::class);
        $directory->expects($this->any())->method('isWritable')->willReturn(FALSE);
        $destination = $this->createMock(Filename::class);
        $destination->expects($this->any())->method('getDirectory')->willReturn($directory);

        $this->expectException(FileNotWritableException::class);

        $activator = new BatPharActivator('foo ##PHAR_FILENAME## bar');
        $activator->activate(new Filename('some.phar'), $destination);
    }

    protected function tearDown() {
        if (file_exists(__DIR__ . '/some.bat')) {
            unlink(__DIR__ . '/some.bat');
        }
    }

}
