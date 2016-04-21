<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\RemoveCommandConfig
 */
class RemoveCommandConfigTest extends \PHPUnit_Framework_TestCase {

    public function testGetTargetDirectory() {
        $directory = $this->getDirectoryMock();
        $locator = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new RemoveCommandConfig($this->getOptionsMock(), $locator);

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    public function testGetPharName() {
        $options = $this->getOptionsMock();
        $options->method('getArgument')->with(0)->willReturn('foo');

        $commandConfig = new RemoveCommandConfig($options, $this->getTargetDirectoryLocatorMock());

        $this->assertSame('foo', $commandConfig->getPharName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(TargetDirectoryLocator::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->getMockBuilder(Directory::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Options
     */
    private function getOptionsMock() {
        return $this->getMockBuilder(Options::class)
            ->disableOriginalConstructor()->getMock();
    }

}
