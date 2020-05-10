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

    public function testInErrorBecauseBothOldAndNewExists(): void {
        $migration = $this->createMigration(true, true, true, true);

        $this->assertTrue($migration->inError());
    }

    public function testNotInErrorWithMissingOld(): void {
        $migration = $this->createMigration(false, true, true, true);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithMissingNew(): void {
        $migration = $this->createMigration(true, false, false, true);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithMissingNewButExistingDirectory(): void {
        $migration = $this->createMigration(true, true, false, true);

        $this->assertFalse($migration->inError());
    }

    public function testNotInErrorWithBothOldAndNewMissing(): void {
        $migration = $this->createMigration(false, false, false, true);

        $this->assertFalse($migration->inError());
    }

    public function testCanMigrate(): void {
        $migration = $this->createMigration(true, false, false, true);

        $this->assertTrue($migration->canMigrate());
    }

    public function testCanMigrateWithNewDirectoryExisting(): void {
        $migration = $this->createMigration(true, true, false, true);

        $this->assertTrue($migration->canMigrate());
    }

    public function testCannotMigrateBecauseMissingOld(): void {
        $migration = $this->createMigration(false, true, true, true);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectAuthXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
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
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive-auth.xml')->putContent('<?xml><root xmlns="https://phar.io/phive-auth">Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectAuthXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
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
        }

        $environment = $this->createMock(Environment::class);
        $environment
            ->method('getWorkingDirectory')
            ->willReturn($workingDirectory);

        return new ProjectAuthXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getInputMock($this, $accepted)
        );
    }
}
