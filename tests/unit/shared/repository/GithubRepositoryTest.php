<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\GithubRepository
 */
class GithubRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedReleases() {
        $pharAlias = $this->getPharAliasMock();
        $pharAlias->method('__toString')->willReturn('foo');

        $entry1 = $this->getGithubEntry('5.3.0', 'https://example.com/foo-5.3.0.phar');
        $entry2 = $this->getGithubEntry('5.2.11', 'https://example.com/broken');
        $entry3 = $this->getGithubEntry('5.2.12', 'https://example.com/foo-5.2.12.phar');

        $jsonData = $this->getJsonDataMock();
        $jsonData->method('getParsed')
            ->willReturn([$entry1, $entry2, $entry3]);

        $expectedReleases = new ReleaseCollection();
        $expectedReleases->add(new Release('foo', new Version('5.3.0'), new Url('https://example.com/foo-5.3.0.phar')));
        $expectedReleases->add(new Release('foo', new Version('5.2.12'), new Url('https://example.com/foo-5.2.12.phar')));

        $repository = new GithubRepository($jsonData);
        $this->assertEquals(
            $expectedReleases,
            $repository->getReleasesByAlias($pharAlias)
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

        $entry = new \stdClass();
        $entry->tag_name = $version;
        $entry->assets = [$asset];

        return $entry;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|JsonData
     */
    private function getJsonDataMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(JsonData::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharAlias
     */
    private function getPharAliasMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PharAlias::class);
    }


}
