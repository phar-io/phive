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

use function file_exists;
use function file_put_contents;
use function glob;
use function mkdir;
use function rmdir;
use function unlink;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PharInstaller
 * @covers \PharIo\Phive\WindowsPharInstaller
 */
class WindowsPharInstallerTest extends TestCase {
    public const TMP_DIR = __DIR__ . '/tmp';

    protected function setUp(): void {
        $this->cleanupTmpDirectory();
        mkdir(self::TMP_DIR);
    }

    protected function tearDown(): void {
        $this->cleanupTmpDirectory();
    }

    public function testCreatesExpectedCopyAndBatFile(): void {
        $output = $this->createOutputMock();

        file_put_contents(self::TMP_DIR . '/foo.phar', 'foo');

        $phar        = new File(new Filename(self::TMP_DIR . '/foo.phar'), 'foo');
        $destination = new Filename(self::TMP_DIR . '/foo.copy');

        $installer = new WindowsPharInstaller($output, 'foo PLACEHOLDER');
        $installer->install($phar, $destination, true);

        $this->assertFileExists(self::TMP_DIR . '/foo.bat');
    }

    /**
     * @return Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function createOutputMock() {
        return $this->createMock(Output::class);
    }

    private function cleanupTmpDirectory(): void {
        if (file_exists(self::TMP_DIR)) {
            foreach (glob(self::TMP_DIR . '/foo.*') as $file) {
                unlink($file);
            }
            rmdir(self::TMP_DIR);
        }
    }
}
