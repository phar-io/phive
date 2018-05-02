<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\PharActivatorLocator
 */
class PharActivatorLocatorTest extends TestCase {

    /**
     * @dataProvider returnsExpectedActivatorDataProvider
     *
     * @param $environment
     * @param $expectedFactoryMethod
     */
    public function testReturnsExpectedActivator($environment, $expectedFactoryMethod) {
        $activator = $this->getPharActivatorMock();

        $factory = $this->getPharActivatorFactoryMock();
        $factory->expects($this->once())
            ->method($expectedFactoryMethod)
            ->willReturn($activator);

        $locator = new PharActivatorLocator($factory);

        $actual = $locator->getPharActivator($environment);

        $this->assertSame($activator, $actual);
    }

    public function returnsExpectedActivatorDataProvider() {
        return [
            [$this->getWindowsEnvironmentMock(), 'getWindowsPharActivator'],
            [$this->getEnvironmentMock(), 'getSymlinkPharActivator']
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|WindowsEnvironment
     */
    private function getWindowsEnvironmentMock() {
        return $this->createMock(WindowsEnvironment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharActivatorFactory
     */
    private function getPharActivatorFactoryMock() {
        return $this->createMock(PharActivatorFactory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharActivator
     */
    private function getPharActivatorMock() {
        return $this->createMock(PharActivator::class);
    }

}
