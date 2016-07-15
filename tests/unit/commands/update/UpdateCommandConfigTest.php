<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\UpdateCommandConfig
 */
class UpdateCommandConfigTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetTargetDirectory() {
        $directory = $this->getDirectoryMock();
        $locatorMock = $this->getTargetDirectoryLocatorMock();
        $locatorMock->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new UpdateCommandConfig(
            $this->getOptionsMock(), $this->getPhiveXmlConfigMock(), $locatorMock
        );
        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
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

    public function testGetRequestedPharsWithoutFilter() {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('getArgumentCount')
            ->willReturn(0);

        $configuredPhars = [
            new ConfiguredPhar('phpab', new ExactVersionConstraint('1.12.0')),
            new ConfiguredPhar('phpdoc', new AnyVersionConstraint()),
            new ConfiguredPhar('phpunit', new AnyVersionConstraint())
        ];

        $phpabPhar = new RequestedPharAlias(new PharAlias('phpab', new ExactVersionConstraint('1.12.0')));
        $phpdocPhar = new RequestedPharAlias(new PharAlias('phpdoc', new AnyVersionConstraint()));
        $phpunitPhar = new RequestedPharAlias(new PharAlias('phpunit', new AnyVersionConstraint()));

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn($configuredPhars);

        $commandConfig = new UpdateCommandConfig($options, $phiveXmlConfig, $this->getTargetDirectoryLocatorMock());
        $this->assertEquals([$phpabPhar,$phpdocPhar,$phpunitPhar], $commandConfig->getRequestedPhars());
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

        $configuredPhars = [
            new ConfiguredPhar('phpunit', new AnyVersionConstraint()),
            new ConfiguredPhar('phpab', new ExactVersionConstraint('1.12.0')),
            new ConfiguredPhar('phpdoc', new AnyVersionConstraint())
        ];

        $phpunitPhar = new RequestedPharAlias(new PharAlias('phpunit', new AnyVersionConstraint()));
        $phpabPhar = new RequestedPharAlias(new PharAlias('phpab', new ExactVersionConstraint('1.12.0')));

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn($configuredPhars);

        $expected = [$phpunitPhar, $phpabPhar];

        $commandConfig = new UpdateCommandConfig($options, $phiveXmlConfig, $this->getTargetDirectoryLocatorMock());
        $this->assertEquals($expected, $commandConfig->getRequestedPhars());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
    }

}


