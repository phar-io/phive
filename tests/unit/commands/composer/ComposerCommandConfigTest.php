<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\ComposerCommandConfig
 */
class ComposerCommandConfigTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetWorkingDirectory() {
        $directory = $this->getDirectoryMock();

        $config = $this->getConfigMock();
        $config->method('getWorkingDirectory')->willReturn($directory);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $config,
            $this->getPhiveXmlConfigMock()
        );

        $this->assertSame($directory, $commandConfig->getWorkingDirectory());
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
            $this->getConfigMock(),
            $this->getPhiveXmlConfigMock()
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
            $this->getConfigMock(),
            $this->getPhiveXmlConfigMock()
        );

        $this->assertSame($value, $commandConfig->makeCopy());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $value
     */
    public function testDoNotAddToPhiveXml($value) {
        $options = $this->getOptionsMock();
        $options->method('isSwitch')->with('temporary')->willReturn($value);

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getConfigMock(),
            $this->getPhiveXmlConfigMock()
        );

        $this->assertSame($value, $commandConfig->doNotAddToPhiveXml());
    }

    public function testGetComposerFilenname() {
        $composerFilename = new Filename('/foo/composer.json');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->with('composer.json')->willReturn($composerFilename);

        $config = $this->getConfigMock();
        $config->method('getWorkingDirectory')->willReturn($directory);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $config,
            $this->getPhiveXmlConfigMock()
        );

        $this->assertSame($composerFilename, $commandConfig->getComposerFilename());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Config
     */
    private function getConfigMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Config::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PhiveXmlConfig::class);
    }

}
