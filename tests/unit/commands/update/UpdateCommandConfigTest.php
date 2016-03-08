<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\UpdateCommandConfig
 */
class UpdateCommandConfigTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetWorkingDirectory() {
        $directory = $this->getDirectoryMock();
        $config = $this->getConfigMock();

        $config->expects($this->once())
            ->method('getWorkingDirectory')
            ->willReturn($directory);

        $commandConfig = new UpdateCommandConfig($this->getOptionsMock(), $config, $this->getPhiveXmlConfigMock());
        $this->assertSame($directory, $commandConfig->getWorkingDirectory());
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

    public function testGetRequestedPharsWithoutFilter() {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('getArgumentCount')
            ->willReturn(0);

        $phpunitPhar = RequestedPhar::fromAlias(new PharAlias('phpunit', new AnyVersionConstraint()));
        $phpabPhar = RequestedPhar::fromAlias(new PharAlias('phpunit', new ExactVersionConstraint('1.12.0')));
        $phpdocPhar = RequestedPhar::fromAlias(new PharAlias('phpunit', new AnyVersionConstraint()));

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn([$phpabPhar, $phpdocPhar, $phpunitPhar]);

        $commandConfig = new UpdateCommandConfig($options, $this->getConfigMock(), $phiveXmlConfig);
        $this->assertEquals([$phpabPhar, $phpdocPhar, $phpunitPhar], $commandConfig->getRequestedPhars());
    }

    public function testGetRequestedPharsWithFilter() {
        $options = $this->getOptionsMock();
        $options->expects($this->any())
            ->method('getArgumentCount')
            ->willReturn(2);

        $options->expects($this->any())
            ->method('getArgument')
            ->willReturnMap([
                [0, 'phpab'],
                [1, 'phpunit']
            ]);

        $phpunitPhar = RequestedPhar::fromAlias(new PharAlias('phpunit', new AnyVersionConstraint()));
        $phpabPhar = RequestedPhar::fromAlias(new PharAlias('phpab', new ExactVersionConstraint('1.12.0')));
        $phpdocPhar = RequestedPhar::fromAlias(new PharAlias('phpdoc', new AnyVersionConstraint()));

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn([$phpabPhar, $phpdocPhar, $phpunitPhar]);

        $expected = [$phpabPhar, $phpunitPhar];

        $commandConfig = new UpdateCommandConfig($options, $this->getConfigMock(), $phiveXmlConfig);
        $this->assertEquals($expected, $commandConfig->getRequestedPhars());
    }


}


