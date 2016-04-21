<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\Release
 */
class ReleaseTest extends \PHPUnit_Framework_TestCase {

    public function testGetVersion() {
        $version = $this->getVersionMock();
        $release = new Release('foo', $version, $this->getUrlMock());

        $this->assertSame($version, $release->getVersion());
    }

    public function testGetUrl() {
        $url = $this->getUrlMock();
        $release = new Release('foo', $this->getVersionMock(), $url);

        $this->assertSame($url, $release->getUrl());
    }

    public function testGetName() {
        $release = new Release('bar', $this->getVersionMock(), $this->getUrlMock());
        $this->assertSame('bar', $release->getName());
    }

    public function testGetExpectedHash() {
        $hash = $this->getHashMock();
        $release = new Release('foo', $this->getVersionMock(), $this->getUrlMock(), $hash);

        $this->assertSame($hash, $release->getExpectedHash());
    }

    public function testHasExpectedHash() {
        $release = new Release('foo', $this->getVersionMock(), $this->getUrlMock());
        $this->assertFalse($release->hasExpectedHash());

        $release = new Release('foo', $this->getVersionMock(), $this->getUrlMock(), $this->getHashMock());
        $this->assertTrue($release->hasExpectedHash());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Url
     */
    private function getUrlMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Url::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Version
     */
    private function getVersionMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Version::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Hash
     */
    private function getHashMock() {
        return $this->getMock(Hash::class);
    }

}
