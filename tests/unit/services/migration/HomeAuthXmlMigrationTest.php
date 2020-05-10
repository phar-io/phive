<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HomeAuthXmlMigration
 */
class HomeAuthXmlMigrationTest extends TestCase {
    use MigrationMocks;

    protected function tearDown(): void {
        @\unlink(__DIR__ . '/tmp/auth.xml');
        @\unlink(__DIR__ . '/tmp/phive-auth.xml');
        @\unlink(__DIR__ . '/tmp/phive-auth.xml.backup');
        parent::tearDown();
    }

    public function testInErrorBecauseBothNewAndOldExists(): void {
        $migration = $this->createMigration(['phive-auth.xml', 'auth.xml']);

        $this->assertTrue($migration->inError());
    }

    public function testNotInErrorWithMissingOld(): void {
        $migration = $this->createMigration(['auth.xml']);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithMissingNew(): void {
        $migration = $this->createMigration(['phive-auth.xml']);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithBothOldAndNewMissing(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->inError());
    }

    public function testCanMigrate(): void {
        $migration = $this->createMigration(['phive-auth.xml']);

        $this->assertTrue($migration->canMigrate());
    }

    public function testCannotMigrateBecauseMissingOld(): void {
        $migration = $this->createMigration([]);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomeAuthXmlMigration($config, $this->getInputMock($this, false));

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/auth.xml');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive-auth.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive-auth.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/auth.xml', '<?xml><root xmlns="https://phar.io/auth">Foobar</root>');
    }

    public function testMigrateRename(): void {
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomeAuthXmlMigration($config, $this->getInputMock($this, true));

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/auth.xml');
        $this->assertFileExists(__DIR__ . '/tmp/phive-auth.xml.backup');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive-auth.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive-auth.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/auth.xml', '<?xml><root xmlns="https://phar.io/auth">Foobar</root>');
    }

    private function createMigration(array $existingFiles): HomeAuthXmlMigration {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, $existingFiles));

        return new HomeAuthXmlMigration($config, $this->getInputMock($this, true));
    }
}
