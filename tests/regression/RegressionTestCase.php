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
namespace PharIo\Phive\RegressionTests;

use function chdir;
use function chmod;
use function copy;
use function exec;
use function file_exists;
use function filesize;
use function glob;
use function is_dir;
use function is_link;
use function mkdir;
use function readlink;
use function rmdir;
use function sprintf;
use function symlink;
use function unlink;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Phive\LocalPhiveXmlConfig;
use PharIo\Phive\Phar;
use PharIo\Phive\PharRegistry;
use PharIo\Phive\XmlFile;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RegressionTestCase extends TestCase {
    private $pharSize = 0;

    /** @var Directory */
    private $workingDirectory;

    /** @var Directory */
    private $toolsDirectory;

    /** @var PharRegistry */
    private $registry;

    final protected function setUp(): void {
        $this->workingDirectory = new Directory(__DIR__ . '/tmp');
        $this->toolsDirectory   = new Directory($this->workingDirectory . '/tools');
        $this->changeWorkingDirectory($this->workingDirectory);
        $this->createCopyOfPharUnderTest();
        $this->createTemporaryDirectory();
        $this->_setUp();
    }

    final protected function tearDown(): void {
        $this->removeTemporaryDirectory();
        $this->ensurePharIsUnchanged();
        unlink($this->getTestedPharFilename());
        $this->_tearDown();
    }

    protected function _setUp(): void {
    }

    protected function _tearDown(): void {
    }

    protected function changeWorkingDirectory(Directory $directory): void {
        chdir((string)$directory);
    }

    /**
     * @param string $name
     * @param string $version
     * @param string $filename
     */
    protected function addPharToRegistry($name, $version, $filename, ?Filename $usage = null): void {
        $phar = new Phar($name, new Version($version), new File(new Filename($filename), 'foo'));
        $this->getPharRegistry()->addPhar($phar);

        if (null === $usage) {
            return;
        }
        $this->getPharRegistry()->addUsage($phar, $usage);
    }

    protected function runPhiveCommand($command, array $arguments = []) {
        $call = $this->getTestedPharFilename();
        $call .= ' --home ' . (string)$this->getPhiveHomeDirectory();
        $call .= ' ' . $command;

        foreach ($arguments as $argument) {
            $call .= ' ' . $argument;
        }
        $call .= ' 2>&1';
        @exec($call, $outputLines, $resultCode);

        $output = '';

        foreach ($outputLines as $line) {
            $output .= $line . "\n";
        }

        if ($resultCode !== 0) {
            $output = sprintf("PHIVE exited with exit code %d!\nOutput:\n%s", $resultCode, $output);

            throw new RuntimeException($output, $resultCode);
        }

        return $output;
    }

    protected function getWorkingDirectory(): Directory {
        return $this->workingDirectory;
    }

    protected function getToolsDirectory(): Directory {
        return $this->toolsDirectory;
    }

    /**
     * @param string $filename
     */
    protected function usePhiveXmlConfig($filename): void {
        copy($filename, $this->getWorkingDirectory()->file('phive.xml')->asString());
    }

    protected function getPhiveXmlConfig(): LocalPhiveXmlConfig {
        return new LocalPhiveXmlConfig(
            new XmlFile(
                $this->getWorkingDirectory()->file('phive.xml'),
                'https://phar.io/phive',
                'phive'
            ),
            new VersionConstraintParser()
        );
    }

    /**
     * @param string $filename
     * @param string $target
     */
    protected function assertSymlinkTargetEquals($filename, $target): void {
        $this->assertTrue(
            is_link($filename),
            sprintf('Failed asserting that %s is a symlink.', $filename)
        );
        $this->assertEquals($target, readlink($filename));
    }

    /**
     * @param string $filename
     */
    protected function assertFileIsNotASymlink($filename): void {
        $this->assertNotTrue(
            is_link($filename),
            sprintf('Failed asserting that %s is not a symlink.', $filename)
        );
    }

    /**
     * @param string $target
     * @param string $link
     */
    protected function createSymlink($target, $link): void {
        symlink($target, $link);
    }

    protected function getPhiveHomeDirectory(): Directory {
        return new Directory(__DIR__ . '/fixtures/phive-home');
    }

    private function removeDirectory($path): void {
        $files = glob($path . '/*');

        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    }

    private function ensurePharIsUnchanged(): void {
        if ($this->pharSize !== filesize($this->getTestedPharFilename())) {
            $this->fail('The PHAR under test was changed during the test!');
        }
    }

    private function getTestedPharFilename(): string {
        return __DIR__ . '/under-test.php';
    }

    private function getPharFilename(): string {
        return glob(__DIR__ . '/../../build/phar/*.phar')[0];
    }

    private function createTemporaryDirectory(): void {
        if (!file_exists(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    private function removeTemporaryDirectory(): void {
        if (file_exists(__DIR__ . '/tmp')) {
            $this->removeDirectory(__DIR__ . '/tmp');
        }
    }

    private function createCopyOfPharUnderTest(): void {
        $testedPharFilename = $this->getTestedPharFilename();
        copy($this->getPharFilename(), $testedPharFilename);
        chmod($testedPharFilename, 0777);
        $this->pharSize = filesize($testedPharFilename);
    }

    private function getPharRegistry(): PharRegistry {
        if (null === $this->registry) {
            $xmlFilename = $this->getPhiveHomeDirectory()->file('phars.xml')->asString();

            if (file_exists($xmlFilename)) {
                unlink($xmlFilename);
            }
            $this->registry = new PharRegistry(
                new XmlFile(new Filename($xmlFilename), 'https://phar.io/phive/installdb', 'phars'),
                $this->getPhiveHomeDirectory()->child('phars')
            );
        }

        return $this->registry;
    }
}
