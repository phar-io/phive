<?php
namespace PharIo\Phive;

use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers PharIo\Phive\PharDownloader
 */
class PharDownloaderTest extends \PHPUnit_Framework_TestCase {

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

    public function testReturnsExpectedPharFileIfStatusCodeIs200() {
        $url = new Url('https://example.com/foo.phar');
        $release = new Release('foo', new Version('1.0.0'), $url, null);
        $signatureUrl = new Url('https://example.com/foo.phar.asc');
        $downloadedFile = new File(new Filename('foo.phar'), 'foo');
        $this->fileDownloader->download($url)->willReturn($downloadedFile);
        $this->fileDownloader->download($signatureUrl)->willReturn(new File(new Filename('foo.phar.asc'), 'bar'));

        $this->verificationResult->getFingerprint()->willReturn('fooFingerprint');
        $this->verificationResult->wasVerificationSuccessful()->willReturn(true);
        $this->signatureVerifier->verify('foo', 'bar', [])->willReturn($this->verificationResult->reveal());

        $expected = new Phar('foo', new Version('1.0.0'), $downloadedFile, 'fooFingerprint');

        $downloader = new PharDownloader(
            $this->fileDownloader->reveal(), $this->signatureVerifier->reveal(), $this->checksumService->reveal(), $this->getPharRegistryMock()
        );
        $this->assertEquals($expected, $downloader->download($release));
    }

    public function testVerifiesChecksum() {
        $url = new Url('https://example.com/foo.phar');
        $expectedHash = new Sha1Hash(sha1('foo'));
        $release = new Release('foo', new Version('1.0.0'), $url, $expectedHash);
        $signatureUrl = new Url('https://example.com/foo.phar.asc');

        $pharFile = new File(new Filename('foo.phar'), 'foo');
        $this->fileDownloader->download($url)->willReturn($pharFile);
        $this->fileDownloader->download($signatureUrl)->willReturn(new File(new Filename('foo.phar.asc'), 'bar'));

        $this->verificationResult->getFingerprint()->willReturn('foo');
        $this->verificationResult->wasVerificationSuccessful()->willReturn(true);
        $this->signatureVerifier->verify('foo', 'bar', [])->willReturn($this->verificationResult->reveal());

        $this->checksumService->verify($expectedHash, $pharFile)->shouldBeCalled()->willReturn(true);

        $downloader = new PharDownloader(
            $this->fileDownloader->reveal(), $this->signatureVerifier->reveal(), $this->checksumService->reveal(), $this->getPharRegistryMock()
        );
        $downloader->download($release);
    }

    /**
     * @expectedException \PharIo\Phive\VerificationFailedException
     */
    public function testThrowsExceptionIfSignatureVerificationFails() {
        $url = new Url('https://example.com/foo.phar');
        $expectedHash = new Sha1Hash(sha1('foo'));
        $release = new Release('foo', new Version('1.0.0'), $url, $expectedHash);
        $signatureUrl = new Url('https://example.com/foo.phar.asc');

        $pharFile = new File(new Filename('foo.phar'), 'foo');
        $this->fileDownloader->download($url)->willReturn($pharFile);
        $this->fileDownloader->download($signatureUrl)->willReturn(new File(new Filename('foo.phar.asc'), 'bar'));

        $this->signatureVerifier->verify('foo', 'bar', [])->willReturn($this->verificationResult->reveal());

        $downloader = new PharDownloader(
            $this->fileDownloader->reveal(), $this->signatureVerifier->reveal(), $this->checksumService->reveal(), $this->getPharRegistryMock()
        );
        $downloader->download($release);
    }

    /**
     * @expectedException \PharIo\Phive\VerificationFailedException
     */
    public function testThrowsExceptionIfChecksumVerificationFails() {
        $url = new Url('https://example.com/foo.phar');
        $expectedHash = new Sha1Hash(sha1('foo'));
        $release = new Release('foo', new Version('1.0.0'), $url, $expectedHash);
        $signatureUrl = new Url('https://example.com/foo.phar.asc');

        $pharFile = new File(new Filename('foo.phar'), 'foo');
        $this->fileDownloader->download($url)->willReturn($pharFile);
        $this->fileDownloader->download($signatureUrl)->willReturn(new File(new Filename('foo.phar.asc'), 'bar'));

        $this->verificationResult->wasVerificationSuccessful()->willReturn(true);
        $this->signatureVerifier->verify('foo', 'bar', [])->willReturn($this->verificationResult->reveal());

        $this->checksumService->verify($expectedHash, $pharFile)->shouldBeCalled()->willReturn(false);

        $downloader = new PharDownloader(
            $this->fileDownloader->reveal(), $this->signatureVerifier->reveal(), $this->checksumService->reveal(), $this->getPharRegistryMock()
        );
        $downloader->download($release);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharRegistry
     */
    private function getPharRegistryMock() {
        $mock = $this->getMockWithoutInvokingTheOriginalConstructor(PharRegistry::class);
        $mock->method('getKnownSignatureFingerprints')->willReturn([]);
        return $mock;
    }

}



