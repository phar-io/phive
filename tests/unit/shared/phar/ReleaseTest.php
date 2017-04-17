<?php
namespace PharIo\Phive;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Release
 */
class ReleaseTest extends TestCase {

    public function testGetVersion() {
        $version = $this->getVersionMock();
        $release = new Release('foo', $version, $this->getUrlMock(), $this->getSignatureUrlMock());

        $this->assertSame($version, $release->getVersion());
    }

    public function testGetUrl() {
        $url = $this->getUrlMock();
        $release = new Release('foo', $this->getVersionMock(), $url, $this->getSignatureUrlMock());

        $this->assertSame($url, $release->getUrl());
    }

    public function testGetName() {
        $release = new Release('bar', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock());
        $this->assertSame('bar', $release->getName());
    }

    public function testGetExpectedHash() {
        $hash = $this->getHashMock();
        $release = new Release('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock(), $hash);

        $this->assertSame($hash, $release->getExpectedHash());
    }

    public function testHasExpectedHash() {
        $release = new Release('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock());
        $this->assertFalse($release->hasExpectedHash());

        $release = new Release('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getSignatureUrlMock(), $this->getHashMock());
        $this->assertTrue($release->hasExpectedHash());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharUrl
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Hash
     */
    private function getHashMock() {
        return $this->createMock(Hash::class);
    }

}
