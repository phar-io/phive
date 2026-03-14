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
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PhiveXmlConfigFileLocator
 */
class PhiveXmlConfigFileLocatorTest extends TestCase {
    public function testWarnAboutDoubleConfigFile(): void {
        $environmentMock = $this->getEnvironmentMock();
        $environmentMock->method('getWorkingDirectory')
            ->willReturn(new Directory(__DIR__ . '/fixtures/doubleConfig'));

        $outputMock = $this->getOutputMock();
        $outputMock
            ->expects($this->once())
            ->method('writeWarning')
            ->with('Both .phive/phars.xml and phive.xml shouldn\'t be defined at the same time. Please prefer using .phive/phars.xml');

        $locator = new PhiveXmlConfigFileLocator(
            $environmentMock,
            new Config($environmentMock, new Options()),
            new Options(),
            $outputMock
        );

        $locator->getFile(false);
    }

    public function testReturnsConfigPathWhenConfigOptionIsSet(): void {
        $customConfigPath = '/custom/path/phars.xml';
        $options          = new Options();
        $options->setOption('config', $customConfigPath);

        $locator = new PhiveXmlConfigFileLocator(
            $this->getEnvironmentMock(),
            $this->createMock(Config::class),
            $options,
            $this->getOutputMock()
        );

        $result = $locator->getFile();

        $this->assertEquals($customConfigPath, $result->asString());
    }

    /**
     * @return Output|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOutputMock() {
        return $this->createMock(Output::class);
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }
}
