<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\ComposerCommandConfig
 */
class ComposerCommandConfigTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetTargetDirectory() {
        $directory = $this->getDirectoryMock();

        $locator = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $locator,
            $directory
        );

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $value
     */
    public function testInstallGlobally($value) {
        $options = $this->getOptionsMock();
        $options->method('isSwitch')->with('global')->willReturn($value);

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($value, $commandConfig->installGlobally());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $value
     */
    public function testMakeCopy($value) {
        $options = $this->getOptionsMock();
        $options->method('isSwitch')->with('copy')->willReturn($value);

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($value, $commandConfig->makeCopy());
    }

    /**
     * @dataProvider doNotAddToPhiveXmlProvider
     *
     * @param bool $temporaryValue
     * @param bool $globalValue
     * @param bool $expected
     */
    public function testDoNotAddToPhiveXml($temporaryValue, $globalValue, $expected) {
        $options = $this->getOptionsMock();
        $options->method('isSwitch')->willReturnMap(
            [
                ['temporary', $temporaryValue],
                ['global', $globalValue]
            ]
        );

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($expected, $commandConfig->doNotAddToPhiveXml());
    }

    public function testGetComposerFilename() {
        $composerFilename = new Filename('/foo/composer.json');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->with('composer.json')->willReturn($composerFilename);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $directory
        );

        $this->assertSame($composerFilename, $commandConfig->getComposerFilename());
    }

    /**
     * @return array
     */
    public static function doNotAddToPhiveXmlProvider() {
        return [
            [true, false, true],
            [true, true, true],
            [false, true, true],
            [false, false, false],
        ];
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PhiveXmlConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(TargetDirectoryLocator::class);
    }

}
