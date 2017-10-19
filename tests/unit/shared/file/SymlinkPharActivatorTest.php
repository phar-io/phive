<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\SymlinkPharActivator
 */
class SymlinkPharActivatorTest extends TestCase {

    public function setUp() {
        if (stripos(PHP_OS, 'win') === 0) {
            $this->markTestSkipped('PHP does not support symlinks on Windows.');
        }
        $this->deleteTestSymlink();
    }

    public function tearDown() {
        $this->deleteTestSymlink();
    }

    public function testActivateCreatesExpectedSymlink() {
        $targetPhar = new Filename(__DIR__ .'/fixtures/some.phar');
        $symlinkFilename = new Filename(__DIR__ . '/symlink');

        $activator = new SymlinkPharActivator();
        $activator->activate($targetPhar, $symlinkFilename);

        $this->assertTrue(is_link($symlinkFilename->asString()));
    }

    public function testActiveReturnsExpectedFilename() {
        $targetPhar = new Filename(__DIR__ .'/fixtures/some.phar');
        $symlinkFilename = new Filename(__DIR__ . '/symlink');

        $activator = new SymlinkPharActivator();
        $actual = $activator->activate($targetPhar, $symlinkFilename);

        $this->assertEquals($symlinkFilename ,$actual);
    }

    private function deleteTestSymlink() {
        $filename = __DIR__ . '/symlink';
        if (!file_exists($filename)) {
            return;
        }
        unlink($filename);
    }

}
