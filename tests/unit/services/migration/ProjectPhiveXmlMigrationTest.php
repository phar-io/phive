<?php declare(strict_types = 1);
namespace PharIo\Phive;

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
        @\unlink(__DIR__ . '/tmp/phive.xml');
        @\unlink(__DIR__ . '/tmp/phive.xml.backup');
        @\unlink(__DIR__ . '/tmp/.phive/phars.xml');
        @\rmdir(__DIR__ . '/tmp/.phive');
        parent::tearDown();
    }

    /**
     * phive.xml exists, and .phive/phars.xml exists
     */
    public function testInError(): void {
        $migration = $this->createMigration(true, true, true, true);

        $this->assertTrue($migration->inError());
    }

    /**
     * No phive.xml, and .phive/phars.xml exists
     */
    public function testNotInError1(): void {
        $migration = $this->createMigration(false, true, true, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive.xml exists, and no .phive/phars.xml
     */
    public function testNotInError2(): void {
        $migration = $this->createMigration(true, false, false, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive.xml exists, and no .phive/phars.xml
     */
    public function testNotInError2_2(): void {
        $migration = $this->createMigration(true, true, false, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * No phive.xml, and no .phive/phars.xml
     */
    public function testNotInError3(): void {
        $migration = $this->createMigration(false, false, false, true);

        $this->assertFalse($migration->inError());
    }

    /**
     * phive.xml exists, and no .phive/phars.xml
     */
    public function testCanMigrate(): void {
        $migration = $this->createMigration(true, false, false, true);

        $this->assertTrue($migration->canMigrate());
    }
    /**
     * phive.xml exists, and no .phive/phars.xml
     */
    public function testCanMigrate2(): void {
        $migration = $this->createMigration(true, true, false, true);

        $this->assertTrue($migration->canMigrate());
    }

    /**
     * Missing phive.xml
     */
    public function testCannotMigrate(): void {
        $migration = $this->createMigration(false, true, true, true);

        $this->assertFalse($migration->canMigrate());
    }

    public function testMigrate(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectPhiveXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getOutputMock($this),
            $this->getInputMock($this, false)
        );

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/.phive/phars.xml');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/.phive/phars.xml', '<?xml><root>Foobar</root>');
    }

    public function testMigrateRename(): void {
        // Create context
        $directory = new Directory(__DIR__ . '/tmp');
        $directory->file('phive.xml')->putContent('<?xml><root>Foobar</root>');

        $environment = $this->createMock(Environment::class);
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $migration = new ProjectPhiveXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getOutputMock($this),
            $this->getInputMock($this, true)
        );

        $migration->migrate();

        $this->assertFileExists(__DIR__ . '/tmp/.phive/phars.xml');
        $this->assertFileExists(__DIR__ . '/tmp/phive.xml.backup');

        if (\method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/tmp/phive.xml');
        } else {
            $this->assertFileNotExists(__DIR__ . '/tmp/phive.xml');
        }
        $this->assertStringEqualsFile(__DIR__ . '/tmp/.phive/phars.xml', '<?xml><root>Foobar</root>');
    }

    private function createMigration(bool $haveLegacy, bool $haveNewDirectory, bool $haveNewFile, bool $accepted): ProjectPhiveXmlMigration {
        $workingDirectory = $this->traitGetDirectoryWithFileMock($this, [$haveLegacy ? 'phive.xml' : null]);

        if ($haveNewDirectory) {
            $workingDirectory->method('hasChild')->with('.phive')->willReturn(true);
            $workingDirectory->method('child')->with('.phive')->willReturn(
                $this->traitGetDirectoryWithFileMock($this, [$haveNewFile ? 'phars.xml' : null])
            );
        } else {
            $workingDirectory->method('hasChild')->with('.phive')->willReturn(false);
            $workingDirectory->expects($this->never())->method('child');
        }

        $environment = $this->createMock(Environment::class);
        $environment
            ->method('getWorkingDirectory')
            ->willReturn($workingDirectory);

        return new ProjectPhiveXmlMigration(
            $environment,
            new Config($environment, $this->getOptionsMock($this)),
            $this->getOutputMock($this),
            $this->getInputMock($this, $accepted)
        );
    }
}
