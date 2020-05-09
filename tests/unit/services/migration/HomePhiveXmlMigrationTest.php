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

    /**
     * phive.xml exists, and global.xml exists
     */
    public function testInError(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phive.xml', 'global.xml']));

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertTrue($migration->inError());
    }

    /**
     * No phive.xml, and global.xml exists
     */
    public function testNotInError1(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['global.xml']));

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * phive.xml exists, and no global.xml
     */
    public function testNotInError2(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phive.xml']));

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * No phive.xml, and no global.xml
     */
    public function testNotInError3(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, []));

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * phive.xml exists, and no global.xml
     */
    public function testCanMigrate(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phive.xml']));

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertTrue($migration->canMigrate());
    }

    /**
     * Missing phive.xml
     */
    public function testCannotMigrate(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, []));

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, false));

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
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomePhiveXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

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
}
