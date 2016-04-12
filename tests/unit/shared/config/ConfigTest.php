<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testGetHomeDirectory() {
        $homeDirectory = $this->getDirectoryMock();

        $directory = $this->getDirectoryMock();
        $directory->method('child')->with('.phive')->willReturn($homeDirectory);

        $environment = $this->getEnvironmentMock();
        $environment->method('getHomeDirectory')->willReturn($directory);

        $config = new Config($environment, $this->getOptionsMock());
        $this->assertSame($homeDirectory, $config->getHomeDirectory());
    }

    public function testGetHomeDirectoryOverriddenByCliOptions() {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('hasOption')
            ->with('home')
            ->willReturn(true);
        $options->expects($this->once())
            ->method('getOption')
            ->with('home')
            ->willReturn(__DIR__);

        $expectedDirectory = new Directory(__DIR__);

        $environment = $this->getEnvironmentMock();
        $environment->expects($this->never())->method('getHomeDirectory');

        $config = new Config($environment, $options);
        $this->assertEquals($expectedDirectory, $config->getHomeDirectory());
    }

    public function testGetWorkingDirectory() {
        $directory = $this->getDirectoryMock();

        $environment = $this->getEnvironmentMock();
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $config = new Config($environment, $this->getOptionsMock());
        $this->assertSame($directory, $config->getWorkingDirectory());
    }

    public function testGetGPGBinaryPath() {
        $config = new Config($this->getEnvironmentMock(), $this->getOptionsMock());
        $this->assertSame('/usr/bin/gpg', $config->getGPGBinaryPath());
    }

    public function testGetSourcesListUrl() {
        $config = new Config($this->getEnvironmentMock(), $this->getOptionsMock());
        $this->assertEquals(
            new Url('https://phar.io/data/repositories.xml'),
            $config->getSourcesListUrl()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Options
     */
    private function getOptionsMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Options::class);
    }

}
