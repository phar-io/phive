<?php
namespace PharIo\Phive;

use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;

/**
 * @covers PharIo\Phive\ReleaseCollection
 */
class ReleaseCollectionTest extends \PHPUnit_Framework_TestCase {

    public function testAdd() {
        $releases = new ReleaseCollection();
        $this->assertAttributeEmpty('releases', $releases);

        $release = $this->getReleaseMock();
        $releases->add($release);

        $this->assertAttributeContains($release, 'releases', $releases);
    }

    public function testGetLatestThrowsException() {
        $releases = new ReleaseCollection();
        $this->expectException(ReleaseException::class);

        $releases->getLatest($this->getVersionConstraintMock());
    }

    public function testGetLatestReturnsExpectedRelease() {
        $releases = new ReleaseCollection();

        $release1 = $this->getReleaseMock();
        $release1->method('getVersion')
            ->willReturn(new Version('1.0.2'));
        $release2 = $this->getReleaseMock();
        $release2->method('getVersion')
            ->willReturn(new Version('0.9.3'));
        $release3 = $this->getReleaseMock();
        $release3->method('getVersion')
            ->willReturn(new Version('1.0.4'));

        $constraint = $this->getVersionConstraintMock();
        $constraint->expects($this->at(0))
            ->method('complies')
            ->willReturn(true);
        $constraint->expects($this->at(1))
            ->method('complies')
            ->willReturn(false);
        $constraint->expects($this->at(2))
            ->method('complies')
            ->willReturn(true);

        $releases->add($release1);
        $releases->add($release2);
        $releases->add($release3);

        $this->assertSame($release3, $releases->getLatest($constraint));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Release
     */
    private function getReleaseMock() {
        return $this->createMock(Release::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|VersionConstraint
     */
    private function getVersionConstraintMock() {
        return $this->createMock(VersionConstraint::class);
    }

}
