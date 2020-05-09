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

    /**
     * phars.xml exists, and database.xml exists
     */
    public function testInError(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phars.xml', 'registry.xml']));

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertTrue($migration->inError());
    }

    /**
     * No phars.xml, and database.xml exists
     */
    public function testNotInError1(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['registry.xml']));

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * phars.xml exists, and no database.xml
     */
    public function testNotInError2(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phars.xml']));

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * No phars.xml, and no database.xml
     */
    public function testNotInError3(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, []));

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * phars.xml exists, and no database.xml
     */
    public function testCanMigrate(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phars.xml']));

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertTrue($migration->canMigrate());
    }

    /**
     * Missing phars.xml
     */
    public function testCannotMigrate(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, []));

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phars.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, false));

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
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phars.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePharsXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

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
}
