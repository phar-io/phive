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
use function sys_get_temp_dir;
use function unlink;
use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HomePhiveXmlMigration
 */
class HomePhiveXmlMigrationTest extends TestCase {
    use MigrationMocks;

    protected function tearDown(): void {
        @unlink(sys_get_temp_dir() . '/phive.xml');
        @unlink(sys_get_temp_dir() . '/phive.xml.backup');
        @unlink(sys_get_temp_dir() . '/global.xml');
        parent::tearDown();
    }

    public function testInErrorBecauseBothOldAndNewExists(): void {
        $migration = $this->createMigration(['phive.xml', 'global.xml']);

        $this->assertTrue($migration->inError());
    }

    public function testNotInErrorWithMissingOld(): void {
        $migration = $this->createMigration(['global.xml']);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive.xml exists, and no global.xml.
     */
    public function testNotInErrorWithMissingNew(): void {
        $migration = $this->createMigration(['phive.xml']);

        $this->assertFalse($migration->inError());
    }

    /**
     * No phive.xml, and no global.xml.
     */
    public function testNotInErrorWithBothOldAndNewMissing(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->inError());
    }

    public function testCanMigrate(): void {
        $migration = $this->createMigration(['phive.xml']);

        $this->assertTrue($migration->canMigrate());
    }

    public function testCannotMigrateBecauseMissingOld(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        $directory = new Directory(sys_get_temp_dir());
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePhiveXmlMigration($config);

        $migration->migrate();

        $this->assertFileExists(sys_get_temp_dir() . '/global.xml');

        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(sys_get_temp_dir() . '/phive.xml');
        } else {
            $this->assertFileNotExists(sys_get_temp_dir() . '/phive.xml');
        }
        $this->assertStringEqualsFile(sys_get_temp_dir() . '/global.xml', '<?xml><root>Foobar</root>');
    }

    private function createMigration(array $existingFiles): HomePhiveXmlMigration {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, $existingFiles));

        return new HomePhiveXmlMigration($config);
    }
}
