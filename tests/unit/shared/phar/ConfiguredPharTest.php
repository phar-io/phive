<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ConfiguredPhar
 */
class ConfiguredPharTest  extends TestCase {
    public function testGetName() {
        $name = 'somePhar';
        $configuredPhar = new ConfiguredPhar($name, $this->getVersionConstraintMock());
        $this->assertSame($name, $configuredPhar->getName());
    }

    public function testGetVersionConstraint() {
        $constraint = $this->getVersionConstraintMock();
        $configuredPhar = new ConfiguredPhar('foo', $constraint);
        $this->assertSame($constraint, $configuredPhar->getVersionConstraint());
    }

    public function testIsInstalled() {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->assertFalse($configuredPhar->isInstalled());

        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock());
        $this->assertTrue($configuredPhar->isInstalled());
    }

    public function testGetInstalledVersion() {
        $version = $this->getVersionMock();
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $version);
        $this->assertSame($version, $configuredPhar->getInstalledVersion());
    }

    public function testGetInstalledVersionThrowsExceptionIfPharIsNotInstalled() {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->expectException(ConfiguredPharException::class);
        $configuredPhar->getInstalledVersion();
    }

    public function testHasLocation() {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->assertFalse($configuredPhar->hasLocation());

        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock(), $this->getFilenameMock());
        $this->assertTrue($configuredPhar->hasLocation());
    }

    public function testGetLocation() {
        $location = $this->getFilenameMock();
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock(), $location);
        $this->assertSame($location, $configuredPhar->getLocation());
    }

    public function testGetLocationThrowsExceptionWhenNoLocationIsSet() {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), $this->getVersionMock(), null);
        $this->expectException(ConfiguredPharException::class);
        $configuredPhar->getLocation();
    }

    public function testHasUrl() {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock(), null, null, null);
        $this->assertFalse($configuredPhar->hasUrl());

        $configuredPhar = new ConfiguredPhar(
            'foo', $this->getVersionConstraintMock(), null, null,
            $this->createMock(PharUrl::class)
        );
        $this->assertTrue($configuredPhar->hasUrl());
    }

    public function testGetUrl() {
        $url = $this->createMock(PharUrl::class);
        $configuredPhar = new ConfiguredPhar(
            'foo', $this->getVersionConstraintMock(), null, null,
            $url
        );
        $this->assertSame($url, $configuredPhar->getUrl());
    }

    public function testGetUrlThrowsExceptionWhenNoneIsSet() {
        $configuredPhar = new ConfiguredPhar('foo', $this->getVersionConstraintMock());
        $this->expectException(ConfiguredPharException::class);
        $configuredPhar->getUrl();
    }

    public function testIsCopy() {
        $configuredPhar = new ConfiguredPhar(
            'foo', $this->getVersionConstraintMock(), null, null, null,
            true
        );

        $this->assertTrue($configuredPhar->isCopy());

        $configuredPhar = new ConfiguredPhar(
            'foo', $this->getVersionConstraintMock(), null, null, null,
            false
        );

        $this->assertFalse($configuredPhar->isCopy());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filename
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|VersionConstraint
     */
    private function getVersionConstraintMock() {
        return $this->createMock(VersionConstraint::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Version
     */
    private function getVersionMock() {
        return $this->createMock(Version::class);
    }
}
