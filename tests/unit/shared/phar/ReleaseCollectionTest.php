<?php
namespace PharIo\Phive;

use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ReleaseCollection
 */
class ReleaseCollectionTest extends TestCase {

    public function testAdd() {
        $releases = new ReleaseCollection();
        $this->assertAttributeEmpty('releases', $releases);

        $release = $this->getReleaseMock();
        $releases->add($release);

        $this->assertAttributeContains($release, 'releases', $releases);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SupportedRelease
     */
    private function getReleaseMock() {
        return $this->createMock(SupportedRelease::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|VersionConstraint
     */
    private function getVersionConstraintMock() {
        return $this->createMock(VersionConstraint::class);
    }

}
