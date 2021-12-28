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

use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\ConfiguredPhar
 */
class ConfiguredPharTest extends TestCase {
    public function testGetName(): void {
        $name           = 'somePhar';
        $configuredPhar = new ConfiguredPhar($name, $this->getVersionConstraintMock());
        $this->assertSame($name, $configuredPhar->getName());
    }

    public function testGetVersionConstraint(): void {
        $constraint     = $this->getVersionConstraintMock();
        $configuredPhar = new ConfiguredPhar('foo', $constraint);
        $this->assertSame($constraint, $configuredPhar->getVersionConstraint());
    }

    public function testIsInstalled(): void {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->assertFalse($configuredPhar->isInstalled());

        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock());
        $this->assertTrue($configuredPhar->isInstalled());
    }

    public function testGetInstalledVersion(): void {
        $version        = $this->getVersionMock();
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $version);
        $this->assertSame($version, $configuredPhar->getInstalledVersion());
    }

    public function testGetInstalledVersionThrowsExceptionIfPharIsNotInstalled(): void {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->expectException(ConfiguredPharException::class);
        $configuredPhar->getInstalledVersion();
    }

    public function testHasLocation(): void {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->assertFalse($configuredPhar->hasLocation());

        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock(), $this->getFilenameMock());
        $this->assertTrue($configuredPhar->hasLocation());
    }

    public function testGetLocation(): void {
        $location       = $this->getFilenameMock();
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock(), $location);
        $this->assertSame($location, $configuredPhar->getLocation());
    }

    public function testGetLocationThrowsExceptionWhenNoLocationIsSet(): void {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock(), null);
        $this->expectException(ConfiguredPharException::class);
        $configuredPhar->getLocation();
    }

    public function testHasUrl(): void {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), null, null, null);
        $this->assertFalse($configuredPhar->hasUrl());

        $configuredPhar = new ConfiguredPhar(
            'foo',
            $this->getVersionConstraintMock(),
            null,
            null,
            $this->createMock(PharUrl::class)
        );
        $this->assertTrue($configuredPhar->hasUrl());
    }

    public function testGetUrl(): void {
        $url            = $this->createMock(PharUrl::class);
        $configuredPhar = new ConfiguredPhar(
            'foo',
            $this->getVersionConstraintMock(),
            null,
            null,
            $url
        );
        $this->assertSame($url, $configuredPhar->getUrl());
    }

    public function testGetUrlThrowsExceptionWhenNoneIsSet(): void {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->expectException(ConfiguredPharException::class);
        $configuredPhar->getUrl();
    }

    public function testIsCopy(): void {
        $configuredPhar = new ConfiguredPhar(
            'foo',
            $this->getVersionConstraintMock(),
            null,
            null,
            null,
            true
        );

        $this->assertTrue($configuredPhar->isCopy());

        $configuredPhar = new ConfiguredPhar(
            'foo',
            $this->getVersionConstraintMock(),
            null,
            null,
            null,
            false
        );

        $this->assertFalse($configuredPhar->isCopy());
    }

    /**
     * @return Filename|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|VersionConstraint
     */
    private function getVersionConstraintMock() {
        return $this->createMock(VersionConstraint::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Version
     */
    private function getVersionMock() {
        return $this->createMock(Version::class);
    }
}
