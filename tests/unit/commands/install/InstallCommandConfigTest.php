<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\InstallCommandConfig
 */
class InstallCommandConfigTest extends TestCase {

    use ScalarTestDataProvider;

    public function testGetTargetDirectory() {
        $directory = $this->getDirectoryMock();
        $locator = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new InstallCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $locator
        );

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }


    /**
     * @dataProvider makeCopyProvider
     *
     * @param bool $hasCopyOption
     * @param bool $hasGlobalOption
     * @param bool $expected
     */
    public function testMakeCopy($hasCopyOption, $hasGlobalOption, $expected) {
        $options = $this->getOptionsMock();
        $options->method('hasOption')->willReturnMap(
            [
                ['copy', $hasCopyOption],
                ['global', $hasGlobalOption]
            ]
        );

        $commandConfig = new InstallCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($expected, $commandConfig->makeCopy());
    }

    public function testGetRequestedPharsFromPhiveXmlConfig() {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('getArgumentCount')
            ->willReturn(0);

        $configuredPhar1 = new ConfiguredPhar('Some Phar', new AnyVersionConstraint());
        $configuredPhar2 = new ConfiguredPhar('Some Other Phar', new ExactVersionConstraint('1.2.3'), new Version('1.2.3'));

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn([$configuredPhar1, $configuredPhar2]);

        $expectedPhars = [
            new RequestedPhar(new PharAlias('Some Phar'), new AnyVersionConstraint(), new AnyVersionConstraint()),
            new RequestedPhar(new PharAlias('Some Other Phar'), new ExactVersionConstraint('1.2.3'), new ExactVersionConstraint('1.2.3'))
        ];

        $commandConfig = new InstallCommandConfig(
            $options,
            $phiveXmlConfig,
            $this->getEnvironmentMock(),
            $this->getTargetDirectoryLocatorMock()
        );
        $this->assertEquals($expectedPhars, $commandConfig->getRequestedPhars());
    }

    public function testGetRequestedPharsFromCliOptions() {
        $options = $this->getOptionsMock();
        $options->expects($this->any())
            ->method('getArgumentCount')
            ->willReturn(3);

        $options->expects($this->any())
            ->method('getArgument')
            ->willReturnMap([
                [0, 'https://example.com/foo-1.2.0.phar'],
                [1, 'phpunit'],
                [2, 'phpab@1.12.0']
            ]);

        $expected = [
            new RequestedPhar(new PharUrl('https://example.com/foo-1.2.0.phar'), new ExactVersionConstraint('1.2.0'), new ExactVersionConstraint('1.2.0')),
            new RequestedPhar(new PharAlias('phpunit'), new AnyVersionConstraint(), new AnyVersionConstraint()),
            new RequestedPhar(new PharAlias('phpab'), new ExactVersionConstraint('1.12.0'), new ExactVersionConstraint('1.12.0')),
        ];

        $commandConfig = new InstallCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $this->getTargetDirectoryLocatorMock()
        );
        $this->assertEquals($expected, $commandConfig->getRequestedPhars());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $switch
     */
    public function testDoNotAddToPhiveXml($switch) {
        $options = $this->getOptionsMock();
        $options->expects($this->any())
            ->method('hasOption')
            ->willReturn($switch);

        $config = new InstallCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $this->getTargetDirectoryLocatorMock()
        );

        $this->assertSame($switch, $config->doNotAddToPhiveXml());
    }
    
    /**
     * @return array
     */
    public static function makeCopyProvider() {
        return [
            [true, false, true],
            [false, false, false],
            [false, true, true]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

}


