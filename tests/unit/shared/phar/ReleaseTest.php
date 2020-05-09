<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\SupportedRelease
 */
class ReleaseTest extends TestCase {
    public function testGetVersion(): void {
        $version = $this->getVersionMock();
        $release = new SupportedRelease('foo', $version, $this->getUrlMock(), $this->getSignatureUrlMock());

        $this->assertSame($version, $release->getVersion());
    }

    public function testGetUrl(): void {
        $url     = $this->getUrlMock();
        $release = new SupportedRelease('foo', $this->getVersionMock(), $url, $this->getSignatureUrlMock());

        $this->assertSame($url, $release->getUrl());
    }

    public function testGetName(): void {
        $release = new SupportedRelease('bar', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock());
        $this->assertSame('bar', $release->getName());
    }

    public function testGetExpectedHash(): void {
        $hash    = $this->getHashMock();
        $release = new SupportedRelease('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock(), $hash);

        $this->assertSame($hash, $release->getExpectedHash());
    }

    public function testHasExpectedHash(): void {
        $release = new SupportedRelease('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock());
        $this->assertFalse($release->hasExpectedHash());

        $release = new SupportedRelease('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock(), $this->getHashMock());
        $this->assertTrue($release->hasExpectedHash());
    }

    /**
     * @return PharUrl|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getUrlMock() {
        return $this->createMock(PharUrl::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Url
     */
    private function getSignatureUrlMock() {
        return $this->createMock(Url::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Version
     */
    private function getVersionMock() {
        return $this->createMock(Version::class);
    }

    /**
     * @return Hash|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getHashMock() {
        return $this->createMock(Hash::class);
    }
}
