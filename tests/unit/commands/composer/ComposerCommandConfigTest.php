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
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\ComposerCommandConfig
 */
class ComposerCommandConfigTest extends TestCase {
    use ScalarTestDataProvider;

    public function testGetTargetDirectory(): void {
        $directory = $this->getDirectoryMock();

        $locator = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $locator,
            $directory
        );

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    public function testGetComposerFilename(): void {
        $composerFilename = new Filename('/foo/composer.json');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->with('composer.json')->willReturn($composerFilename);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $this->getTargetDirectoryLocatorMock(),
            $directory
        );

        $this->assertSame($composerFilename, $commandConfig->getComposerFilename());
    }

    /**
     * @return Directory|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return Options|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }

    /**
     * @return PhiveXmlConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPhiveXmlConfigMock() {
        return $this->createMock(PhiveXmlConfig::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }
}
