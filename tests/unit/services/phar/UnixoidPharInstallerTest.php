<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\UnixoidPharInstaller
 * @covers \PharIo\Phive\PharInstaller
 */
class UnixoidPharInstallerTest extends TestCase {
    public const TMP_DIR = __DIR__ . '/tmp';

    protected function setUp(): void {
        $this->cleanupTmpDirectory();
        \mkdir(self::TMP_DIR);
    }

    protected function tearDown(): void {
        $this->cleanupTmpDirectory();
    }

    public function testCreatesExpectedCopy(): void {
        $output = $this->createOutputMock();

        \file_put_contents(self::TMP_DIR . '/foo.phar', 'foo');

        $phar        = new File(new Filename(self::TMP_DIR . '/foo.phar'), 'foo');
        $destination = new Filename(self::TMP_DIR . '/foo.copy');

        $installer = new UnixoidPharInstaller($output);
        $installer->install($phar, $destination, true);

        $this->assertFileExists(self::TMP_DIR . '/foo.copy');

        $this->assertFileEquals(self::TMP_DIR . '/foo.phar', self::TMP_DIR . '/foo.copy');
    }

    public function testCreatesExpectedSymlink(): void {
        $output = $this->createOutputMock();

        \file_put_contents(self::TMP_DIR . '/foo.phar', 'foo');

        $phar        = new File(new Filename(self::TMP_DIR . '/foo.phar'), 'foo');
        $destination = new Filename(self::TMP_DIR . '/foo.link');

        $installer = new UnixoidPharInstaller($output);
        $installer->install($phar, $destination, false);

        $this->assertFileExists(self::TMP_DIR . '/foo.link');

        $this->assertSymlinkPointsToFile(self::TMP_DIR . '/foo.phar', self::TMP_DIR . '/foo.link');
    }

    private function assertSymlinkPointsToFile($file, $symlink): void {
        $this->assertSame($file, \readlink($symlink));
    }

    /**
     * @return Output|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createOutputMock() {
        return $this->createMock(Output::class);
    }

    private function cleanupTmpDirectory(): void {
        if (\file_exists(self::TMP_DIR)) {
            foreach (\glob(self::TMP_DIR . '/foo.*') as $file) {
                \unlink($file);
            }
            \rmdir(self::TMP_DIR);
        }
    }
}
