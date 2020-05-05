<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\Phive\Cli\Options;
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

class AuthXmlConfigFileLocatorTest extends TestCase {
    public function testWarnAboutDoubleConfigFile(): void {
        $environmentMock = $this->getEnvironmentMock();
        $environmentMock->method('getWorkingDirectory')
            ->willReturn(new Directory(__DIR__ . '/fixtures/doubleConfig'));

        $outputMock = $this->getOutputMock();
        $outputMock
            ->expects($this->once())
            ->method('writeWarning')
            ->with('Both .phive/auth.xml and phive-auth.xml shouldn\'t be defined. Please prefer using .phive/auth.xml');

        $locator = new AuthXmlConfigFileLocator(
            $environmentMock,
            new Config($environmentMock, new Options()),
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
}
