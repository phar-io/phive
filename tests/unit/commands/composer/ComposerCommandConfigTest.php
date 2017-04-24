<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ComposerCommandConfig
 */
class ComposerCommandConfigTest extends TestCase {

    use ScalarTestDataProvider;

    public function testGetTargetDirectory() {
        $directory = $this->getDirectoryMock();

        $locator = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $locator,
            $directory
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

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($expected, $commandConfig->makeCopy());
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
        $options->method('hasOption')->willReturnMap(
            [
                ['temporary', $temporaryValue],
                ['global', $globalValue]
            ]
        );

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
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
            $this->getEnvironmentMock(),
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
     * @return \PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

}
