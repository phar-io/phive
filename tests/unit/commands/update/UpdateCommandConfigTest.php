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
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\UpdateCommandConfig
 */
class UpdateCommandConfigTest extends TestCase {
    use ScalarTestDataProvider;

    public function testGetTargetDirectory(): void {
        $directory   = $this->getDirectoryMock();
        $locatorMock = $this->getTargetDirectoryLocatorMock();
        $locatorMock->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new UpdateCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $locatorMock
        );
        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    public function testGetRequestedPharsWithoutFilter(): void {
        $options = $this->getOptionsMock();
        $options->expects($this->once())
            ->method('getArgumentCount')
            ->willReturn(0);

        $configuredPhars = [
            new ConfiguredPhar('phpab', new ExactVersionConstraint('1.12.0')),
            new ConfiguredPhar('phpdoc', new AnyVersionConstraint()),
            new ConfiguredPhar('phpunit', new AnyVersionConstraint())
        ];

        $phpabPhar = new RequestedPhar(
            new PharAlias('phpab'),
            new ExactVersionConstraint('1.12.0'),
            new ExactVersionConstraint('1.12.0')
        );
        $phpdocPhar = new RequestedPhar(
            new PharAlias('phpdoc'),
            new AnyVersionConstraint(),
            new AnyVersionConstraint()
        );
        $phpunitPhar = new RequestedPhar(
            new PharAlias('phpunit'),
            new AnyVersionConstraint(),
            new AnyVersionConstraint()
        );

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn($configuredPhars);

        $commandConfig = new UpdateCommandConfig($options, $phiveXmlConfig, $this->getTargetDirectoryLocatorMock());
        $this->assertEquals([$phpabPhar, $phpdocPhar, $phpunitPhar], $commandConfig->getRequestedPhars());
    }

    public function testGetRequestedPharsWithFilter(): void {
        $options = $this->getOptionsMock();
        $options->expects($this->any())
            ->method('getArgumentCount')
            ->willReturn(2);

        $options->expects($this->any())
            ->method('getArgument')
            ->willReturnMap([
                [0, 'phpab'],
                [1, 'phpunit']
            ]);

        $configuredPhars = [
            new ConfiguredPhar('phpunit', new AnyVersionConstraint()),
            new ConfiguredPhar('phpab', new ExactVersionConstraint('1.12.0')),
            new ConfiguredPhar('phpdoc', new AnyVersionConstraint())
        ];

        $phpunitPhar = new RequestedPhar(
            new PharAlias('phpunit'),
            new AnyVersionConstraint(),
            new AnyVersionConstraint()
        );
        $phpabPhar = new RequestedPhar(
            new PharAlias('phpab'),
            new ExactVersionConstraint('1.12.0'),
            new ExactVersionConstraint('1.12.0')
        );

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->once())
            ->method('getPhars')
            ->willReturn($configuredPhars);

        $expected = [$phpunitPhar, $phpabPhar];

        $commandConfig = new UpdateCommandConfig($options, $phiveXmlConfig, $this->getTargetDirectoryLocatorMock());
        $this->assertEquals($expected, $commandConfig->getRequestedPhars());
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

    /**
     * @return PhiveXmlConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPhiveXmlConfigMock() {
        return $this->createMock(PhiveXmlConfig::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
    }
}
