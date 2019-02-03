<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Version\VersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ReleaseCollection
 */
class ReleaseCollectionTest extends TestCase {
    public function testAdd(): void {
        $releases = new ReleaseCollection();
        $this->assertCount(0, $releases);

        $release = $this->getReleaseMock();
        $releases->add($release);

        $this->assertCount(1, $releases);
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
