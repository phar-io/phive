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

    /**
     * phive-auth.xml exists, and auth.xml exists
     */
    public function testInError(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phive-auth.xml', 'auth.xml']));

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertTrue($migration->inError());
    }

    /**
     * No phive-auth.xml, and auth.xml exists
     */
    public function testNotInError1(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['auth.xml']));

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * phive-auth.xml exists, and no auth.xml
     */
    public function testNotInError2(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phive-auth.xml']));

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * No phive-auth.xml, and no auth.xml
     */
    public function testNotInError3(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, []));

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->inError());
    }

    /**
     * phive-auth.xml exists, and no auth.xml
     */
    public function testCanMigrate(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config
            ->method('getHomeDirectory')
            ->willReturn($this->getDirectoryWithFileMock($this, ['phive-auth.xml']));

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertTrue($migration->canMigrate());
    }

    /**
     * Missing phive-auth.xml
     */
    public function testCannotMigrate(): void {
        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($this->getDirectoryWithFileMock($this, []));

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, false));

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
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $config = $this->createPartialMock(Config::class, ['getHomeDirectory']);
        $config->method('getHomeDirectory')->willReturn($directory);

        $migration = new HomeAuthXmlMigration($config, $this->getOutputMock($this), $this->getInputMock($this, true));

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
}
