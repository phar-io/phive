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
use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\TargetDirectoryLocator
 */
class TargetDirectoryLocatorTest extends TestCase {
    public function testGetTargetDirectoryReturnsDefault(): void {
        $directory = $this->getDirectoryMock();
        $config    = $this->getConfigMock();

        $config->expects($this->once())
            ->method('getToolsDirectory')
            ->willReturn($directory);

        $locator = new TargetDirectoryLocator($config, $this->getPhiveXmlConfigMock(), $this->getOptionsMock());

        $this->assertSame($directory, $locator->getTargetDirectory());
    }

    public function testGetTargetDirectoryReturnsDirectoryFromCliOptions(): void {
        $cliOptions = $this->getOptionsMock();
        $cliOptions->expects($this->once())
            ->method('hasOption')->with('target')->willReturn(true);
        $cliOptions->expects($this->once())
            ->method('getOption')->with('target')->willReturn(__DIR__);

        $expectedDirectory = new Directory(__DIR__);

        $locator = new TargetDirectoryLocator($this->getConfigMock(), $this->getPhiveXmlConfigMock(), $cliOptions);

        $this->assertEquals($expectedDirectory, $locator->getTargetDirectory());
    }

    public function testGetTargetDirectoryReturnsDirectoryFromPhiveXmlConfig(): void {
        $directory = $this->getDirectoryMock();

        $xmlConfig = $this->getPhiveXmlConfigMock();
        $xmlConfig->expects($this->once())
            ->method('hasTargetDirectory')
            ->willReturn(true);
        $xmlConfig->expects($this->once())
            ->method('getTargetDirectory')
            ->willReturn($directory);

        $locator = new TargetDirectoryLocator($this->getConfigMock(), $xmlConfig, $this->getOptionsMock());

        $this->assertSame($directory, $locator->getTargetDirectory());
    }

    /**
     * @return Directory|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return Config|PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfigMock() {
        return $this->createMock(Config::class);
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
}
