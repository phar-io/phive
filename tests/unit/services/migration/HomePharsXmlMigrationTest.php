<?php declare(strict_types = 1);
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

use function method_exists;
use function unlink;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HomePharsXmlMigration
 */
class HomePharsXmlMigrationTest extends TestCase {
    use MigrationMocks;

    protected function tearDown(): void {
        @unlink('/tmp/registry.xml');
        @unlink('/tmp/phars.xml');
        @unlink('/tmp/phars.xml.backup');
        parent::tearDown();
    }

    public function testInErrorBecauseBothOldAndNewExists(): void {
        $migration = $this->createMigration(['phars.xml', 'registry.xml']);

        $this->assertTrue($migration->inError());
    }

    public function testNotInErrorWithOldMissing(): void {
        $migration = $this->createMigration(['registry.xml']);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithNewMissing(): void {
        $migration = $this->createMigration(['phars.xml']);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithBothMissing(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->inError());
    }

    public function testCanMigrate(): void {
        $migration = $this->createMigration(['phars.xml']);

        $this->assertTrue($migration->canMigrate());
    }

    /**
     * Missing phars.xml.
     */
    public function testCannotMigrateBecauseMissingOld(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        $directory = new Directory('/tmp');
        $directory->file('phars.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePharsXmlMigration($config);

        $migration->migrate();

        $this->assertFileExists('/tmp/registry.xml');

        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist('/tmp/phars.xml');
        } else {
            $this->assertFileNotExists('/tmp/phars.xml');
        }
        $this->assertStringEqualsFile('/tmp/registry.xml', '<?xml><root>Foobar</root>');
    }

    private function createMigration(array $existingFiles): HomePharsXmlMigration {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn(
            $this->getHomeDirectoryWithFileMock($this, $existingFiles)
        );

        return new HomePharsXmlMigration($config);
    }

    private function getHomeDirectoryWithFileMock(TestCase $testCase, array $files): Directory {
        $directory = $testCase->createMock(Directory::class);
        $directory->method('file')->willReturnCallback(function ($file) use ($files, $testCase) {
            $fileMock = in_array($file, $files, true) ? $this->getFileExistsMock($testCase) : $this->getFileMissingMock($testCase);

            if ($file === 'phars.xml') {
                $file = $this->createMock(File::class);
                $file->method('getContent')->willReturn('... xmlns="https://phar.io/phive/installdb" ...');
                $fileMock->method('read')->willReturn($file);
            }

            return $fileMock;
        });

        return $directory;
    }
}
