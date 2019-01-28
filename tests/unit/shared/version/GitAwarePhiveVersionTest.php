<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\GitAwarePhiveVersion
 * @covers \PharIo\Phive\PhiveVersion
 */
class GitAwarePhiveVersionTest extends TestCase {
    public function testGetVersionStringReturnsTagFromGit(): void {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new GitAwarePhiveVersion($git);

        $this->assertContains('0.2.0-67-gd2a5e31', $version->getVersionString());
    }

    public function testGetVersionReturnsFallbackVersionIfNoGitRepositoryIsPresent(): void {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(false);

        $version = new GitAwarePhiveVersion($git);

        $this->assertEquals(GitAwarePhiveVersion::UNKNOWN_VERSION, $version->getVersion());
    }

    public function testGetVersionReturnsFallbackVersionIfGitThrowsException(): void {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willThrowException(new GitException());

        $version = new GitAwarePhiveVersion($git);

        $this->assertEquals(GitAwarePhiveVersion::UNKNOWN_VERSION, $version->getVersion());
    }

    public function testGetVersionReturnsTagFromGit(): void {
        $git = $this->getGitMock();
        $git->method('isRepository')->willReturn(true);
        $git->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new GitAwarePhiveVersion($git);

        $this->assertEquals('0.2.0-67-gd2a5e31', $version->getVersion());
    }

    public function testCachesVersion(): void {
        $git = $this->getGitMock();
        $git->expects($this->once())->method('isRepository')->willReturn(true);
        $git->expects($this->once())->method('getMostRecentTag')->willReturn('0.2.0-67-gd2a5e31');

        $version = new GitAwarePhiveVersion($git);

        $version->getVersion();
        $version->getVersion();
    }

    /**
     * @return Git|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getGitMock() {
        return $this->createMock(Git::class);
    }
}
