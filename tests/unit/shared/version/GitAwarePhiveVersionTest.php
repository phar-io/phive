<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\GitAwarePhiveVersion
 * @covers PharIo\Phive\PhiveVersion
 */
class GitAwarePhiveVersionTest extends \PHPUnit_Framework_TestCase {

    public function testGetVersionStringReturnsTagFromGit() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new GitAwarePhiveVersion($git);

        $this->assertContains('0.2.0-67-gd2a5e31', $version->getVersionString());
    }

    public function testGetVersionReturnsFallbackVersionIfNoGitRepositoryIsPresent() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(false);

        $version = new GitAwarePhiveVersion($git);

        $this->assertEquals(GitAwarePhiveVersion::UNKNOWN_VERSION, $version->getVersion());
    }

    public function testGetVersionReturnsFallbackVersionIfGitThrowsException() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willThrowException(new GitException());

        $version = new GitAwarePhiveVersion($git);

        $this->assertEquals(GitAwarePhiveVersion::UNKNOWN_VERSION, $version->getVersion());
    }

    public function testGetVersionReturnsTagFromGit() {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new GitAwarePhiveVersion($git);

        $this->assertEquals('0.2.0-67-gd2a5e31', $version->getVersion());
    }

    public function testCachesVersion() {
        $git = $this->getGitMock();
        $git->expects($this->once())->method('isRepository')->willReturn(true);
        $git->expects($this->once())->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new GitAwarePhiveVersion($git);

        $version->getVersion();
        $version->getVersion();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Git
     */
    private function getGitMock() {
        return $this->createMock(Git::class);
    }

}
