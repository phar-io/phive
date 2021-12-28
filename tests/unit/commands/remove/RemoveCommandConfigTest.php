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
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\RemoveCommandConfig
 */
class RemoveCommandConfigTest extends TestCase {
    public function testGetTargetDirectory(): void {
        $directory = $this->getDirectoryMock();
        $locator   = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new RemoveCommandConfig($this->getOptionsMock(), $locator);

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    public function testGetPharName(): void {
        $options = $this->getOptionsMock();
        $options->method('getArgument')->with(0)->willReturn('foo');

        $commandConfig = new RemoveCommandConfig($options, $this->getTargetDirectoryLocatorMock());

        $this->assertSame('foo', $commandConfig->getPharName());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
    }

    /**
     * @return Directory|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return Options|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }
}
