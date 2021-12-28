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

use function method_exists;
use function rmdir;
use function unlink;
use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ProjectPhiveXmlMigration
 */
class ProjectPhiveXmlMigrationTest extends TestCase {
    use MigrationMocks {
        MigrationMocks::getDirectoryWithFileMock as traitGetDirectoryWithFileMock;
    }

    protected function tearDown(): void {
        @unlink('/tmp/phive.xml');
        @unlink('/tmp/phive.xml.backup');
        @unlink('/tmp/.phive/phars.xml');
        @rmdir('/tmp/.phive');
        parent::tearDown();
    }

    public function testInErrorBecauseBothOldAndNewExists(): void {
        $migration = $this->createMigration(true, true, true);

        $this->assertTrue($migration->inError());
    }

    public function testNotInErrorWithMissingOld(): void {
        $migration = $this->createMigration(false, true, true);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithMissingNew(): void {
        $migration = $this->createMigration(true, false, true);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithBothOldAndNewMissing(): void {
        $migration = $this->createMigration(false, false, true);

        $this->assertFalse($migration->inError());
    }

    public function testCanMigrate(): void {
        $migration = $this->createMigration(true, false, true);

        $this->assertTrue($migration->canMigrate());
    }

    public function testCannotMigrateBecauseMissingOld(): void {
        $migration = $this->createMigration(false, true, true);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        $directory = new Directory('/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectPhiveXmlMigration(
            new Config($environment, $this->getOptionsMock($this)),
            $this->getInputMock($this, false)
        );

        $migration->migrate();

        $this->assertFileExists('/tmp/.phive/phars.xml');

        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist('/tmp/phive.xml');
        } else {
            $this->assertFileNotExists('/tmp/phive.xml');
        }
        $this->assertStringEqualsFile('/tmp/.phive/phars.xml', '<?xml><root>Foobar</root>');
    }

    public function testMigrateRename(): void {
        $directory = new Directory('/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectPhiveXmlMigration(
            new Config($environment, $this->getOptionsMock($this)),
            $this->getInputMock($this, true)
        );

        $migration->migrate();

        $this->assertFileExists('/tmp/.phive/phars.xml');
        $this->assertFileExists('/tmp/phive.xml.backup');

        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist('/tmp/phive.xml');
        } else {
            $this->assertFileNotExists('/tmp/phive.xml');
        }
        $this->assertStringEqualsFile('/tmp/.phive/phars.xml', '<?xml><root>Foobar</root>');
    }

    private function createMigration(bool $haveLegacy, bool $haveNewFile, bool $accepted): ProjectPhiveXmlMigration {
        $workingDirectory = $this->traitGetDirectoryWithFileMock($this, [$haveLegacy ? 'phive.xml' : null]);

        $workingDirectory->method('hasChild')->with('.phive')->willReturn(true);
        $workingDirectory->method('child')->with('.phive')->willReturn(
            $this->traitGetDirectoryWithFileMock($this, [$haveNewFile ? 'phars.xml' : null])
        );

        $environment = $this->createMock(Environment::class);
        $environment
            ->method('getWorkingDirectory')
            ->willReturn($workingDirectory);

        return new ProjectPhiveXmlMigration(
            new Config($environment, $this->getOptionsMock($this)),
            $this->getInputMock($this, $accepted)
        );
    }
}
