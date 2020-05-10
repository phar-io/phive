<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HomePhiveXmlMigration
 */
class HomePhiveXmlMigrationTest extends TestCase {
    use MigrationMocks;

    protected function tearDown(): void {
        @\unlink(__DIR__ . '/tmp/phive.xml');
        @\unlink(__DIR__ . '/tmp/phive.xml.backup');
        @\unlink(__DIR__ . '/tmp/global.xml');
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
     * phive.xml exists, and no global.xml
     */
    public function testNotInErrorWithMissingNew(): void {
        $migration = $this->createMigration(['phive.xml']);

        $this->assertFalse($migration->inError());
    }

    /**
     * No phive.xml, and no global.xml
     */
    public function testNotInErrorWithBothOldANdNewMissing(): void {
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
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePhiveXmlMigration($config, $this->getInputMock($this, false));

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/global.xml');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/global.xml', '<?xml><root>Foobar</root>');
    }

    public function testMigrateRename(): void {
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePhiveXmlMigration($config, $this->getInputMock($this, true));

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/global.xml');
        $this->assertFileExists(__DIR__ . '/tmp/phive.xml.backup');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/global.xml', '<?xml><root>Foobar</root>');
    }

    private function createMigration(array $existingFiles): HomePhiveXmlMigration {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, $existingFiles));

        return new HomePhiveXmlMigration($config, $this->getInputMock($this, true));
    }
}
