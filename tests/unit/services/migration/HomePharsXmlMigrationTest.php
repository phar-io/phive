<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HomePharsXmlMigration
 */
class HomePharsXmlMigrationTest extends TestCase {
    use MigrationMocks;

    protected function tearDown(): void {
        @\unlink(__DIR__ . '/tmp/registry.xml');
        @\unlink(__DIR__ . '/tmp/phars.xml');
        @\unlink(__DIR__ . '/tmp/phars.xml.backup');
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
     * Missing phars.xml
     */
    public function testCannotMigrateBecauseMissingOld(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phars.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePharsXmlMigration($config, $this->getInputMock($this, false));

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/registry.xml');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phars.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phars.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/registry.xml', '<?xml><root>Foobar</root>');
    }

    public function testMigrateRename(): void {
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phars.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePharsXmlMigration($config, $this->getInputMock($this, true));

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/registry.xml');
        $this->assertFileExists(__DIR__ . '/tmp/phars.xml.backup');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phars.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phars.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/registry.xml', '<?xml><root>Foobar</root>');
    }

    private function createMigration(array $existingFiles): HomePharsXmlMigration {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, $existingFiles));

        return new HomePharsXmlMigration($config, $this->getInputMock($this, true));
    }
}
