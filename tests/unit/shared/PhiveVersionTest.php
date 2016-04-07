<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\PhiveVersion
 */
class PhiveVersionTest extends \PHPUnit_Framework_TestCase {

    public function testGetVersionString() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(false);

        $version = new PhiveVersion($git, '0.3.2');

        $this->assertContains('0.3.2', $version->getVersionString());
    }
    
    public function testGetVersionReturnsFallbackVersionIfNoGitRepositoryIsPresent() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(false);

        $version = new PhiveVersion($git, '1.4.1');

        $this->assertEquals('1.4.1', $version->getVersion());
    }

    public function testGetVersionReturnsFallbackVersionIfGitThrowsException() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willThrowException(new GitException());

        $version = new PhiveVersion($git, '4.1.0');

        $this->assertEquals('4.1.0', $version->getVersion());
    }

    public function testGetVersionReturnsTagFromGit() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new PhiveVersion($git, '4.1.0');

        $this->assertEquals('0.2.0-67-gd2a5e31', $version->getVersion());
    }

    public function testCachesVersion() {
        $git = $this->getGitMock();
        $git->expects($this->once())->method('isRepository')->willReturn(true);
        $git->expects($this->once())->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new PhiveVersion($git, '4.1.0');

        $version->getVersion();
        $version->getVersion();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Git
     */
    private function getGitMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Git::class);
    }
    
}
