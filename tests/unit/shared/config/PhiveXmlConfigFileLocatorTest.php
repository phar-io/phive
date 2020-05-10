<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

class PhiveXmlConfigFileLocatorTest extends TestCase {
    public function testWarnAboutDoubleConfigFile(): void {
        $environmentMock = $this->getEnvironmentMock();
        $environmentMock->method('getWorkingDirectory')
            ->willReturn(new Directory(__DIR__ . '/fixtures/doubleConfig'));

        $outputMock = $this->getOutputMock();
        $outputMock
            ->expects($this->once())
            ->method('writeWarning')
            ->with('Both .phive/phars.xml and phive.xml shouldn\'t be defined. Please prefer using .phive/phars.xml');

        $locator = new PhiveXmlConfigFileLocator(
            $environmentMock,
            $this->getConfigMock(),
            $outputMock
        );

        $locator->getFile(false);
    }

    /**
     * @return Output|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOutputMock() {
        return $this->createMock(Output::class);
    }

    /**
     * @return Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfigMock() {
        return $this->createMock(Config::class);
    }
}
