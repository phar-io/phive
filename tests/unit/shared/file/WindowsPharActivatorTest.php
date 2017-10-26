<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\WindowsPharActivator
 */
class WindowsPharActivatorTest extends TestCase {

    public function setUp() {
        if (0 !== stripos(PHP_OS, 'win')) {
            $this->markTestSkipped('OS is not Windows.');
        }
        $this->deleteTestFiles();
    }

    protected function tearDown() {
        $this->deleteTestFiles();
    }

    public function testLinksPharAndCreatesExpectedBatFile() {
        $testFile = __DIR__ . '/fixtures/some.phar';

        $activator = new WindowsPharActivator('foo ##PHAR_FILENAME## bar');
        $createdFile = $activator->activate(new Filename($testFile), new Filename('some'));
        $createdBatchFile = preg_replace('(\.phar$)', '.bat', $createdFile);

        $this->assertFileEquals($testFile, (string)$createdFile);
        $this->assertEquals('foo %~dp0some.phar bar', file_get_contents($createdBatchFile));
    }

    private function deleteTestFiles() {
        if (file_exists(__DIR__ . '/some.phar')) {
            unlink(__DIR__ . '/some.phar');
        }
        if (file_exists(__DIR__ . '/some.bat')) {
            unlink(__DIR__ . '/some.bat');
        }
    }
}
