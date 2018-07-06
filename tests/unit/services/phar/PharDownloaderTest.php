<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\PharDownloader
 */
class PharDownloaderTest extends TestCase {

    /**
     * @var FileDownloader|ObjectProphecy
     */
    private $fileDownloader;

    /**
     * @var SignatureVerifier|ObjectProphecy
     */
    private $signatureVerifier;

    /**
     * @var ChecksumService|ObjectProphecy
     */
    private $checksumService;

    /**
     * @var VerificationResult|ObjectProphecy
     */
    private $verificationResult;

    public function setUp() {
        $this->fileDownloader = $this->prophesize(FileDownloader::class);
        $this->signatureVerifier = $this->prophesize(SignatureVerifier::class);
        $this->checksumService = $this->prophesize(ChecksumService::class);
        $this->verificationResult = $this->prophesize(VerificationResult::class);
    }

    public function testReturnsExpectedPharFile() {
        $sigUrl = new Url('https://example.com/foo.phar.asc');
        $url = new PharUrl('https://example.com/foo.phar');
        $release = new SupportedRelease('foo', new Version('1.0.0'), $url, $sigUrl);
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
        $this->signatureVerifier->verify('phar-content', 'phar-signature', [])->willReturn($this->verificationResult->reveal());

        $expected = new Phar('foo', new Version('1.0.0'), $downloadedFile, 'fooFingerprint');

        $downloader = new PharDownloader(
            $httpClient->reveal(),
            $this->signatureVerifier->reveal(),
            $this->checksumService->reveal(),
            $this->getPharRegistryMock()
        );
        $this->assertEquals($expected, $downloader->download($release));
    }

    public function testThrowsExceptionIfSignatureVerificationFails() {
        $sigUrl = new Url('https://example.com/foo.phar.asc');
        $url = new PharUrl('https://example.com/foo.phar');
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
        $this->verificationResult->wasVerificationSuccessful()->willReturn(false);
        $this->signatureVerifier->verify('phar-content', 'phar-signature', [])->willReturn($this->verificationResult->reveal());

        $downloader = new PharDownloader(
            $httpClient->reveal(),
            $this->signatureVerifier->reveal(),
            $this->checksumService->reveal(),
            $this->getPharRegistryMock()
        );

        $this->expectException(\PharIo\Phive\VerificationFailedException::class);

        $downloader->download($release);
    }

    public function testThrowsExceptionIfChecksumVerificationFails() {
        $sigUrl = new Url('https://example.com/foo.phar.asc');
        $url = new PharUrl('https://example.com/foo.phar');
        $release = new SupportedRelease('foo', new Version('1.0.0'), $url, $sigUrl, new Sha1Hash(sha1('not-matching')));

        $sigResponse = $this->prophesize(HttpResponse::class);
        $sigResponse->getBody()->willReturn('phar-signature');
        $sigResponse->isSuccess()->willReturn(true);

        $response = $this->prophesize(HttpResponse::class);
        $response->getBody()->willReturn('phar-content');
        $response->isSuccess()->willReturn(true);

        $httpClient = $this->prophesize(HttpClient::class);
        $httpClient->get($url)->willReturn($response->reveal());
        $httpClient->get($sigUrl)->willReturn($sigResponse->reveal());

        $this->signatureVerifier->verify('phar-content', 'phar-signature', [])->willReturn($this->verificationResult->reveal());

        $downloader = new PharDownloader(
            $httpClient->reveal(),
            $this->signatureVerifier->reveal(),
            $this->checksumService->reveal(),
            $this->getPharRegistryMock()
        );

        $this->expectException(\PharIo\Phive\VerificationFailedException::class);

        $downloader->download($release);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharRegistry
     */
    private function getPharRegistryMock() {
        $mock = $this->createMock(PharRegistry::class);
        $mock->method('getKnownSignatureFingerprints')->willReturn([]);
        return $mock;
    }

}
