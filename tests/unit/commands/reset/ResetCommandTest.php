<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\ResetCommand
 */
class ResetCommandTest extends TestCase {
    public function testInstallsExpectedPhars(): void {
        $pharRegistry = $this->getPharRegistryMock();
        $environment  = $this->getEnvironmentMock();
        $installer    = $this->getPharInstallerMock();

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

    public function testOnlyInstallsPharsMatchingProvidedAliases(): void {
        $config       = $this->getConfigMock();
        $pharRegistry = $this->getPharRegistryMock();
        $environment  = $this->getEnvironmentMock();
        $installer    = $this->getPharInstallerMock();

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
     * @return PHPUnit_Framework_MockObject_MockObject|ResetCommandConfig
     */
    private function getConfigMock() {
        return $this->createMock(ResetCommandConfig::class);
    }

    /**
     * @return PharRegistry|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharRegistryMock() {
        return $this->createMock(PharRegistry::class);
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return PharInstaller|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharInstallerMock() {
        return $this->createMock(PharInstaller::class);
    }

    /**
     * @return Directory|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @param string $name
     * @param string $filename
     *
     * @return Phar|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharMock($name, $filename) {
        $file = $this->createMock(File::class);
        $file->method('getFilename')
            ->willReturn(new Filename($filename));

        $mock = $this->createMock(Phar::class);

        $mock->method('getName')
            ->willReturn($name);

        $mock->method('getFile')
            ->willReturn($file);

        return $mock;
    }
}
