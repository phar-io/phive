<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\InstallCommandConfig
 */
class InstallCommandConfigTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetTargetDirectoryReturnsDefault() {
        $directory = $this->getDirectoryMock();
        $config = $this->getConfigMock();

        $config->expects($this->once())
            ->method('getToolsDirectory')
            ->willReturn($directory);

        $commandConfig = new InstallCommandConfig($this->getOptionsMock(), $config, $this->getPhiveXmlConfigMock());

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    public function testGetTargetDirectoryReturnsDirectoryFromPhiveXmlConfig() {
        $directory = $this->getDirectoryMock();

        $xmlConfig = $this->getPhiveXmlConfigMock();
        $xmlConfig->expects($this->once())
            ->method('hasToolsDirectory')
            ->willReturn(true);
        $xmlConfig->expects($this->once())
            ->method('getToolsDirectory')
            ->willReturn($directory);

        $commandConfig = new InstallCommandConfig($this->getOptionsMock(), $this->getConfigMock(), $xmlConfig);

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->getMockBuilder(Directory::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Config
     */
    private function getConfigMock() {
        return $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Options
     */
    private function getOptionsMock() {
        return $this->getMockBuilder(Options::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->getMockBuilder(PhiveXmlConfig::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @dataProvider boolProvider
     *
     * @param $switch
     */
    public function testMakeCopy($switch) {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('isSwitch')
            ->with('copy')
            ->willReturn($switch);

        $commandConfig = new InstallCommandConfig($options, $this->getConfigMock(), $this->getPhiveXmlConfigMock());
        $this->assertSame($switch, $commandConfig->makeCopy());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param $switch
     */
    public function testInstallGlobally($switch) {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('isSwitch')
            ->with('global')
            ->willReturn($switch);

        $commandConfig = new InstallCommandConfig($options, $this->getConfigMock(), $this->getPhiveXmlConfigMock());
        $this->assertSame($switch, $commandConfig->installGlobally());
    }

    public function testGetRequestedPharsFromPhiveXmlConfig() {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('getArgumentCount')
            ->willReturn(0);

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn(['foo']);

        $commandConfig = new InstallCommandConfig($options, $this->getConfigMock(), $phiveXmlConfig);
        $this->assertEquals(['foo'], $commandConfig->getRequestedPhars());
    }

    public function testGetRequestedPharsFromCliOptions() {
        $options = $this->getOptionsMock();
        $options->expects($this->any())
            ->method('getArgumentCount')
            ->willReturn(3);

        $options->expects($this->any())
            ->method('getArgument')
            ->willReturnMap([
                [0, 'https://example.com/foo.phar'],
                [1, 'phpunit'],
                [2, 'phpab@1.12.0']
            ]);

        $expected = [
            RequestedPhar::fromUrl(new Url('https://example.com/foo.phar')),
            RequestedPhar::fromAlias(new PharAlias('phpunit', new AnyVersionConstraint())),
            RequestedPhar::fromAlias(new PharAlias('phpab', new ExactVersionConstraint('1.12.0'))),
        ];

        $commandConfig = new InstallCommandConfig($options, $this->getConfigMock(), $this->getPhiveXmlConfigMock());
        $this->assertEquals($expected, $commandConfig->getRequestedPhars());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $switch
     */
    public function testDoNotAddToPhiveXml($switch) {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('isSwitch')
            ->willReturn($switch);

        $config = new InstallCommandConfig($options, $this->getConfigMock(), $this->getPhiveXmlConfigMock());
        $this->assertSame($switch, $config->doNotAddToPhiveXml());
    }

}


