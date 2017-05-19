<?php
namespace PharIo\Phive;

use PharIo\Version\VersionNumber;
use PHPUnit\Framework\TestCase;

class GitHubVersionTest extends TestCase {

    /**
     * @dataProvider versionPrefixProvider
     *
     * @param string $prefix
     */
    public function testRemovesVersionPrefix($prefix) {
        $version = new GitHubVersion($prefix . '2.9.0');
        $this->assertSame('2.9.0', $version->getVersionString());
        $this->assertEquals(new VersionNumber('2'), $version->getMajor());
        $this->assertEquals(new VersionNumber('9'), $version->getMinor());
        $this->assertEquals(new VersionNumber('0'), $version->getPatch());
    }

    public function testWorksWithNonPrefixedVersion() {
        $version = new GitHubVersion('2.9.0');
        $this->assertSame('2.9.0', $version->getVersionString());
        $this->assertEquals(new VersionNumber('2'), $version->getMajor());
        $this->assertEquals(new VersionNumber('9'), $version->getMinor());
        $this->assertEquals(new VersionNumber('0'), $version->getPatch());
    }

    public function versionPrefixProvider() {
        return [
            ['v'],
            ['V']
        ];
    }
}
