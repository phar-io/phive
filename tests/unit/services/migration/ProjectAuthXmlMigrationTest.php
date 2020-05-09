<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ProjectAuthXmlMigration
 */
class ProjectAuthXmlMigrationTest extends TestCase {
    use MigrationMocks {
        MigrationMocks::getDirectoryWithFileMock as traitGetDirectoryWithFileMock;
    }

    protected function tearDown(): void {
        @\unlink(__DIR__ . '/tmp/phive-auth.xml');
        @\unlink(__DIR__ . '/tmp/phive-auth.xml.backup');
        @\unlink(__DIR__ . '/tmp/.phive/auth.xml');
        @\rmdir(__DIR__ . '/tmp/.phive');
        parent::tearDown();
    }

    /**
     * phive-auth.xml exists, and .phive/auth.xml exists
     */
    public function testInError(): void {
        $migration = $this->createMigration(true, true, true, true);

        $this->assertTrue($migration->inError());
    }

    /**
     * No phive-auth.xml, and .phive/auth.xml exists
     */
    public function testNotInError1(): void {
        $migration = $this->createMigration(false, true, true, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive-auth.xml exists, and no .phive/auth.xml
     */
    public function testNotInError2(): void {
        $migration = $this->createMigration(true, false, false, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive-auth.xml exists, and no .phive/auth.xml
     */
    public function testNotInError2_2(): void {
        $migration = $this->createMigration(true, true, false, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * No phive-auth.xml, and no .phive/auth.xml
     */
    public function testNotInError3(): void {
        $migration = $this->createMigration(false, false, false, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive-auth.xml exists, and no .phive/auth.xml
     */
    public function testCanMigrate(): void {
        $migration = $this->createMigration(true, false, false, true);

        $this->assertTrue($migration->canMigrate());
    }
    /**
     * phive-auth.xml exists, and no .phive/auth.xml
     */
    public function testCanMigrate2(): void {
        $migration = $this->createMigration(true, true, false, true);

        $this->assertTrue($migration->canMigrate());
    }

    /**
     * Missing phive-auth.xml
     */
    public function testCannotMigrate(): void {
        $migration = $this->createMigration(false, true, true, true);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectAuthXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getOutputMock($this),
            $this->getInputMock($this, false)
        );

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/.phive/auth.xml');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive-auth.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive-auth.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/.phive/auth.xml', '<?xml><root xmlns="https://phar.io/auth">Foobar</root>');
    }

    public function testMigrateRename(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectAuthXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getOutputMock($this),
            $this->getInputMock($this, true)
        );

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/.phive/auth.xml');
        $this->assertFileExists(__DIR__ . '/tmp/phive-auth.xml.backup');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive-auth.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive-auth.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/.phive/auth.xml', '<?xml><root xmlns="https://phar.io/auth">Foobar</root>');
    }

    private function createMigration(bool $haveLegacy, bool $haveNewDirectory, bool $haveNewFile, bool $accepted): ProjectAuthXmlMigration {
        $workingDirectory = $this->traitGetDirectoryWithFileMock($this, [$haveLegacy ? 'phive-auth.xml' : null]);

        if ($haveNewDirectory) {
            $workingDirectory->method('hasChild')->with('.phive')->willReturn(true);
            $workingDirectory->method('child')->with('.phive')->willReturn(
                $this->traitGetDirectoryWithFileMock($this, [$haveNewFile ? 'auth.xml' : null])
            );
        } else {
            $workingDirectory->method('hasChild')->with('.phive')->willReturn(false);
            $workingDirectory->expects($this->never())->method('child');
        }

        $environment = $this->createMock(Environment::class);
        $environment
            ->method('getWorkingDirectory')
            ->willReturn($workingDirectory);

        return new ProjectAuthXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getOutputMock($this),
            $this->getInputMock($this, $accepted)
        );
    }
}
