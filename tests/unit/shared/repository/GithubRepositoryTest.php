<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

/**
 * @covers \PharIo\Phive\GithubRepository
 */
class GithubRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedReleases() {
        $pharAlias = $this->getPharAliasMock();
        $pharAlias->method('asString')->willReturn('foo');

        $requestedPhar = $this->getRequestedPharMock();
        $requestedPhar->method('getAlias')->willReturn($pharAlias);

        $entry1 = $this->getGithubEntry('5.3.0', 'https://example.com/foo-5.3.0.phar');
        $entry2 = $this->getGithubEntry('5.2.11', 'https://example.com/broken');
        $entry3 = $this->getGithubEntry('5.2.12', 'https://example.com/foo-5.2.12.phar');

        $jsonData = $this->getJsonDataMock();
        $jsonData->method('getParsed')
            ->willReturn([$entry1, $entry2, $entry3]);

        $expectedReleases = new ReleaseCollection();
        $expectedReleases->add(
            new Release(
                'foo',
                new Version('5.3.0'),
                new PharUrl('https://example.com/foo-5.3.0.phar'),
                new Url('https://example.com/foo-5.3.0.phar.asc')
            )
        );
        $expectedReleases->add(
            new Release(
                'foo',
                new Version('5.2.12'),
                new PharUrl('https://example.com/foo-5.2.12.phar'),
                new Url('https://example.com/foo-5.2.12.phar.asc')
            )
        );

        $repository = new GithubRepository($jsonData);
        $this->assertEquals(
            $expectedReleases,
            $repository->getReleasesByRequestedPhar($requestedPhar)
        );
    }

    /**
     * @param string $version
     * @param string $url
     *
     * @return \stdClass
     */
    private function getGithubEntry($version, $url) {
        $asset = new \stdClass();
        $asset->browser_download_url = $url;

        $sig = new \stdClass();
        $sig->browser_download_url = $url . '.asc';

        $entry = new \stdClass();
        $entry->tag_name = $version;
        $entry->assets = [
            $asset, $sig
        ];

        return $entry;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|JsonData
     */
    private function getJsonDataMock() {
        return $this->createMock(JsonData::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharAlias
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
