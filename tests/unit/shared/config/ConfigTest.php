<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Config
 */
class ConfigTest extends TestCase {
    public function testGetHomeDirectory(): void {
        $homeDirectory = $this->getDirectoryMock();

        $directory = $this->getDirectoryMock();
        $directory->method('child')->with('.phive')->willReturn($homeDirectory);

        $environment = $this->getEnvironmentMock();
        $environment->method('getHomeDirectory')->willReturn($directory);

        $config = new Config($environment, $this->getOptionsMock());
        $this->assertSame($homeDirectory, $config->getHomeDirectory());
    }

    public function testGetHomeDirectoryOverriddenByCliOptions(): void {
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

    public function testGetWorkingDirectory(): void {
        $directory = $this->getDirectoryMock();

        $environment = $this->getEnvironmentMock();
        $environment->method('getWorkingDirectory')->willReturn($directory);

        $config = new Config($environment, $this->getOptionsMock());
        $this->assertSame($directory, $config->getWorkingDirectory());
    }

    public function testGetGPGBinaryPath(): void {
        $filename = new Filename('foo');

        $environment = $this->getEnvironmentMock();
        $environment->method('getPathToCommand')->with('gpg')->willReturn($filename);

        $config = new Config($environment, $this->getOptionsMock());

        $this->assertSame($filename, $config->getGPGBinaryPath());
    }

    public function testGetToolsDirectory(): void {
        $config            = new Config($this->getEnvironmentMock(), $this->getOptionsMock());
        $expectedDirectory = new Directory('tools');
        $this->assertEquals($expectedDirectory, $config->getToolsDirectory());
    }

    public function testThrowsNoGPGBinaryFoundExceptionIfPathToGpgWasNotFound(): void {
        $environment = $this->getEnvironmentMock();
        $environment->method('getPathToCommand')->with('gpg')->willThrowException(new EnvironmentException());

        $config = new Config($environment, $this->getOptionsMock());
        $this->expectException(NoGPGBinaryFoundException::class);

        $config->getGPGBinaryPath();
    }

    public function testGetSourcesListUrl(): void {
        $config = new Config($this->getEnvironmentMock(), $this->getOptionsMock());
        $this->assertEquals(
            new Url('https://phar.io/data/repositories.xml'),
            $config->getSourcesListUrl()
        );
    }

    public function testReturnsExpectedMaxAgeForSourcesList(): void {
        $now      = new \DateTimeImmutable('25.04.2017 12:23:12');
        $config   = new Config($this->getEnvironmentMock(), $this->getOptionsMock(), $now);
        $expected = new \DateTimeImmutable('18.04.2017 12:23:12');

        $this->assertEquals($expected, $config->getMaxAgeForSourcesList());
    }

    public function testGetTrustedKeyIds(): void {
        $expected = new KeyIdCollection();
        $expected->addKeyId('id1');
        $expected->addKeyId('id2');

        $options = $this->getOptionsMock();
        $options->method('hasOption')->with('trust-gpg-keys')->willReturn(true);
        $options->method('getOption')->with('trust-gpg-keys')->willReturn('id1,id2');

        $config = new Config($this->getEnvironmentMock(), $options);
        $actual = $config->getTrustedKeyIds();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return Directory|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return Options|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }
}
