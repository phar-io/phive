<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\GitlabRepository
 */
class GitlabRepositoryTest extends TestCase {
    public function testReturnsExpectedReleases(): void {
        $pharAlias = $this->getPharAliasMock();
        $pharAlias->method('asString')->willReturn('foo');

        $requestedPhar = $this->getRequestedPharMock();
        $requestedPhar->method('getAlias')->willReturn($pharAlias);

        $entry1 = $this->getGitlabEntry('5.3.0', 'https://example.com/foo-5.3.0.phar');
        $entry2 = $this->getGitlabEntry('5.2.11', 'https://example.com/broken');
        $entry3 = $this->getGitlabEntry('5.2.12', 'https://example.com/foo-5.2.12.phar');

        $jsonData = $this->getJsonDataMock();
        $jsonData->method('getParsed')
            ->willReturn([$entry1, $entry2, $entry3]);

        $expectedReleases = new ReleaseCollection();
        $expectedReleases->add(
            new SupportedRelease(
                'foo',
                new GitHubVersion('5.3.0'),
                new PharUrl('https://example.com/foo-5.3.0.phar'),
                new Url('https://example.com/foo-5.3.0.phar.asc')
            )
        );
        $expectedReleases->add(
            new UnSupportedRelease(
                'foo',
                new GitHubVersion('5.2.11'),
                'No downloadable PHAR'
            )
        );
        $expectedReleases->add(
            new SupportedRelease(
                'foo',
                new GitHubVersion('5.2.12'),
                new PharUrl('https://example.com/foo-5.2.12.phar'),
                new Url('https://example.com/foo-5.2.12.phar.asc')
            )
        );

        $repository = new GitlabRepository($jsonData);
        $this->assertEquals(
            $expectedReleases,
            $repository->getReleasesByRequestedPhar($requestedPhar)
        );
    }

    /**
     * @param string $version
     * @param string $url
     */
    private function getGitlabEntry($version, $url): \stdClass {
        $asset       = new \stdClass();
        $asset->url  = $url;
        $asset->name = \basename($url);

        $sig       = new \stdClass();
        $sig->url  = $url . '.asc';
        $sig->name = \basename($url) . '.asc';

        $assets        = new \stdClass();
        $assets->links = [$asset, $sig];

        $entry           = new \stdClass();
        $entry->tag_name = $version;
        $entry->assets   = $assets;

        return $entry;
    }

    /**
     * @return JsonData|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getJsonDataMock() {
        return $this->createMock(JsonData::class);
    }

    /**
     * @return PharAlias|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharAliasMock() {
        return $this->createMock(PharAlias::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }
}
