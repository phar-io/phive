<?php
namespace PharIo\Phive;

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

        $config = new Config($environment);
        $this->assertSame($homeDirectory, $config->getHomeDirectory());
    }

    public function testGetWorkingDirectory() {
        $directory = $this->getDirectoryMock();

        $environment = $this->getEnvironmentMock();
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $config = new Config($environment);
        $this->assertSame($directory, $config->getWorkingDirectory());
    }

    public function testGetGPGBinaryPath() {
        $config = new Config($this->getEnvironmentMock());
        $this->assertSame('/usr/bin/gpg', $config->getGPGBinaryPath());
    }

    public function testGetSourcesListUrl() {
        $config = new Config($this->getEnvironmentMock());
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

}
