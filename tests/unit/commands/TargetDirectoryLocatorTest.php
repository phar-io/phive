<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\TargetDirectoryLocator
 */
class TargetDirectoryLocatorTest extends TestCase {

    public function testGetTargetDirectoryReturnsDefault() {
        $directory = $this->getDirectoryMock();
        $config = $this->getConfigMock();

        $config->expects($this->once())
            ->method('getToolsDirectory')
            ->willReturn($directory);

        $locator = new TargetDirectoryLocator($config, $this->getPhiveXmlConfigMock(), $this->getOptionsMock());

        $this->assertSame($directory, $locator->getTargetDirectory());
    }

    public function testGetTargetDirectoryReturnsDirectoryFromCliOptions() {
        $cliOptions = $this->getOptionsMock();
        $cliOptions->expects($this->once())
            ->method('hasOption')->with('target')->willReturn(true);
        $cliOptions->expects($this->once())
            ->method('getOption')->with('target')->willReturn(__DIR__);

        $expectedDirectory = new Directory(__DIR__);

        $locator = new TargetDirectoryLocator($this->getConfigMock(), $this->getPhiveXmlConfigMock(), $cliOptions);

        $this->assertEquals($expectedDirectory, $locator->getTargetDirectory());
    }

    public function testGetTargetDirectoryReturnsDirectoryFromPhiveXmlConfig() {
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Config
     */
    private function getConfigMock() {
        return $this->createMock(Config::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Options
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->createMock(PhiveXmlConfig::class);
    }

}
