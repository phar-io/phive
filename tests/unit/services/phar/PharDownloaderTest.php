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

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\PharDownloader
 */
class PharDownloaderTest extends TestCase {
    /** @var FileDownloader|ObjectProphecy */
    private $fileDownloader;

    /** @var MockObject|SignatureVerifier */
    private $signatureVerifier;

    /** @var ChecksumService|ObjectProphecy */
    private $checksumService;

    /** @var ObjectProphecy|VerificationResult */
    private $verificationResult;

    protected function setUp(): void {
        $this->fileDownloader     = $this->prophesize(FileDownloader::class);
        $this->signatureVerifier  = $this->createMock(SignatureVerifier::class);
        $this->checksumService    = $this->prophesize(ChecksumService::class);
        $this->verificationResult = $this->prophesize(VerificationResult::class);
    }

    public function testReturnsExpectedPharFile(): void {
        $sigUrl         = new Url('https://example.com/foo.phar.asc');
        $url            = new PharUrl('https://example.com/foo.phar');
        $release        = new SupportedRelease('foo', new Version('1.0.0'), $url, $sigUrl);
        $downloadedFile = new File(new Filename('foo.phar'), 'phar-content');

        $sigResponse = $this->prophesize(HttpResponse::class);
        $sigResponse->getBody()->willReturn('phar-signature');
        $sigResponse->isSuccess()->willReturn(true);

        $response = $this->prophesize(HttpResponse::class);
        $response->getBody()->willReturn('phar-content');
        $response->isSuccess()->willReturn(true);

        $httpClient = $this->prophesize(HttpClient::class);
        $httpClient->get($url)->willReturn($response->reveal());
        $httpClient->get($sigUrl)->willReturn($sigResponse->reveal());

        $this->verificationResult->getFingerprint()->willReturn('fooFingerprint');
        $this->verificationResult->wasVerificationSuccessful()->willReturn(true);
        $this->signatureVerifier->method('verify')->with('phar-content', 'phar-signature', [])
            ->willReturn($this->verificationResult->reveal());

        $expected = new Phar('foo', new Version('1.0.0'), $downloadedFile, 'fooFingerprint');

        $downloader = new PharDownloader(
            $httpClient->reveal(),
            $this->signatureVerifier,
            $this->checksumService->reveal(),
            $this->getPharRegistryMock()
        );
        $this->assertEquals($expected, $downloader->download($release));
    }

    public function testThrowsExceptionIfSignatureVerificationFails(): void {
        $sigUrl  = new Url('https://example.com/foo.phar.asc');
        $url     = new PharUrl('https://example.com/foo.phar');
        $release = new SupportedRelease('foo', new Version('1.0.0'), $url, $sigUrl);

        $sigResponse = $this->prophesize(HttpResponse::class);
        $sigResponse->getBody()->willReturn('phar-signature');
        $sigResponse->isSuccess()->willReturn(true);

        $response = $this->prophesize(HttpResponse::class);
        $response->getBody()->willReturn('phar-content');
        $response->isSuccess()->willReturn(true);

        $httpClient = $this->prophesize(HttpClient::class);
        $httpClient->get($url)->willReturn($response->reveal());
        $httpClient->get($sigUrl)->willReturn($sigResponse->reveal());

        $this->verificationResult->getFingerprint()->willReturn('fooFingerprint');
        $this->verificationResult->getErrorMessage()->willReturn('Some Message');
        $this->verificationResult->wasVerificationSuccessful()->willReturn(false);
        $this->signatureVerifier->method('verify')->with('phar-content', 'phar-signature', [])
            ->willReturn($this->verificationResult->reveal());

        $downloader = new PharDownloader(
            $httpClient->reveal(),
            $this->signatureVerifier,
            $this->checksumService->reveal(),
            $this->getPharRegistryMock()
        );

        $this->expectException(VerificationFailedException::class);

        $downloader->download($release);
    }

    /**
     * @return PharRegistry|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharRegistryMock() {
        $mock = $this->createMock(PharRegistry::class);
        $mock->method('getKnownSignatureFingerprints')->willReturn([]);

        return $mock;
    }
}
