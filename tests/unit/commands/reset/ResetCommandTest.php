<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ResetCommand
 */
class ResetCommandTest extends TestCase {

    public function testInstallsExpectedPhars() {
        $pharRegistry = $this->getPharRegistryMock();
        $environment = $this->getEnvironmentMock();
        $installer = $this->getPharInstallerMock();

        $command = new ResetCommand(
            $this->getConfigMock(),
            $pharRegistry,
            $environment,
            $installer
        );

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('foo'));

        $environment->method('getWorkingDirectory')->willReturn($directory);

        $phars = [
            $this->getPharMock('foo', 'foo.phar'),
            $this->getPharMock('bar', 'bar.phar')
        ];

        $pharRegistry->expects($this->once())
            ->method('getUsedPharsByDestination')
            ->with($directory)
            ->willReturn($phars);

        $installer->expects($this->exactly(2))->method('install');

        $command->execute();
    }

    public function testOnlyInstallsPharsMatchingProvidedAliases() {
        $config = $this->getConfigMock();
        $pharRegistry = $this->getPharRegistryMock();
        $environment = $this->getEnvironmentMock();
        $installer = $this->getPharInstallerMock();

        $command = new ResetCommand(
            $config,
            $pharRegistry,
            $environment,
            $installer
        );

        $config->method('hasAliases')
            ->willReturn(true);

        $config->method('getAliases')
            ->willReturn(['foo', 'baz']);

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('foo'));

        $environment->method('getWorkingDirectory')
            ->willReturn($directory);

        $phars = [
            $this->getPharMock('foo', 'foo.phar'),
            $this->getPharMock('bar', 'bar.phar'),
            $this->getPharMock('baz', 'baz.phar')
        ];

        $pharRegistry->expects($this->once())
            ->method('getUsedPharsByDestination')
            ->with($directory)
            ->willReturn($phars);

        $installer->expects($this->exactly(2))
            ->method('install');

        $command->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResetCommandConfig
     */
    private function getConfigMock() {
        return $this->createMock(ResetCommandConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharRegistry
     */
    private function getPharRegistryMock() {
        return $this->createMock(PharRegistry::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharInstaller
     */
    private function getPharInstallerMock() {
        return $this->createMock(PharInstaller::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @param string $name
     * @param string $filename
     *
     * @return Phar|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharMock($name, $filename) {

        $file = $this->createMock(File::class);
        $file->method('getFilename')
            ->willReturn($filename);

        $mock = $this->createMock(Phar::class);

        $mock->method('getName')
            ->willReturn($name);

        $mock->method('getFile')
            ->willReturn($file);

        return $mock;
    }

}
