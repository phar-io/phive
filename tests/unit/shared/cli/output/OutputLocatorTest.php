<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive\Cli;

use PharIo\Phive\Environment;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\Cli\OutputLocator
 */
class OutputLocatorTest extends TestCase {
    private $factory;

    private $locator;

    private $environment;

    protected function setUp(): void {
        $this->factory     = $this->createOutputFactoryMock();
        $this->locator     = new OutputLocator($this->factory);
        $this->environment = $this->createEnvironmentMock();
    }

    public function testReturnsColoredConsoleOutput(): void {
        $output = $this->createOutputMock();

        $this->factory->expects($this->once())
            ->method('getColoredConsoleOutput')
            ->willReturn($output);

        $this->environment->method('supportsColoredOutput')->willReturn(true);

        $actual = $this->locator->getOutput($this->environment, false);

        $this->assertSame($output, $actual);
    }

    public function testReturnsConsoleOutput(): void {
        $output = $this->createOutputMock();

        $this->factory->expects($this->once())
            ->method('getConsoleOutput')
            ->willReturn($output);

        $this->environment->method('supportsColoredOutput')->willReturn(false);

        $actual = $this->locator->getOutput($this->environment, false);

        $this->assertSame($output, $actual);
    }

    /**
     * @return Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function createOutputMock() {
        return $this->createMock(Output::class);
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function createEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return OutputFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private function createOutputFactoryMock() {
        return $this->createMock(OutputFactory::class);
    }
}
