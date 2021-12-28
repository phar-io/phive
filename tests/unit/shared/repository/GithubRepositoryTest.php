<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\GithubRepository
 */
class GithubRepositoryTest extends TestCase {
    public function testReturnsExpectedReleases(): void {
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
            new SupportedRelease(
                'foo',
                new Version('5.3.0'),
                new PharUrl('https://example.com/foo-5.3.0.phar'),
                new Url('https://example.com/foo-5.3.0.phar.asc')
            )
        );
        $expectedReleases->add(
            new UnSupportedRelease(
                'foo',
                new Version('5.2.11'),
                'No downloadable PHAR'
            )
        );
        $expectedReleases->add(
            new SupportedRelease(
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
     */
    private function getGithubEntry($version, $url): array {
        $asset = ['browser_download_url' => $url];
        $sig   = ['browser_download_url' => $url . '.asc'];

        return [
            'tag_name' => $version,
            'assets'   => [$asset, $sig]
        ];
    }

    /**
     * @return JsonData|PHPUnit_Framework_MockObject_MockObject
     */
    private function getJsonDataMock() {
        return $this->createMock(JsonData::class);
    }

    /**
     * @return PharAlias|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharAliasMock() {
        return $this->createMock(PharAlias::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }
}
