<?php
namespace PharIo\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

    class PharDownloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var FileDownloader|ObjectProphecy
         */
        private $fileDownloader;

        /**
         * @var SignatureService|ObjectProphecy
         */
        private $signatureService;

        /**
         * @var ChecksumService|ObjectProphecy
         */
        private $checksumService;

        public function setUp() {
            $this->fileDownloader = $this->prophesize(FileDownloader::class);
            $this->signatureService = $this->prophesize(SignatureService::class);
            $this->checksumService = $this->prophesize(ChecksumService::class);
        }

        public function testReturnsExpectedPharFileIfStatusCodeIs200() {
            $url = new Url('https://example.com/foo.phar');
            $release = new Release(new Version('1.0.0'), $url, null);
            $signatureUrl = new Url('https://example.com/foo.phar.asc');
            $this->fileDownloader->download($url)->willReturn(new File('foo.phar', 'foo'));
            $this->fileDownloader->download($signatureUrl)->willReturn(new File('foo.phar.asc', 'bar'));

            $this->signatureService->verify('foo', 'bar')->willReturn(true);

            $expected = new File('foo.phar', 'foo');

            $downloader = new PharDownloader($this->fileDownloader->reveal(), $this->signatureService->reveal(), $this->checksumService->reveal());
            $this->assertEquals($expected, $downloader->download($release));
        }

        public function testVerifiesChecksum() {
            $url = new Url('https://example.com/foo.phar');
            $expectedHash = new Sha1Hash(sha1('foo'));
            $release = new Release(new Version('1.0.0'), $url, $expectedHash);
            $signatureUrl = new Url('https://example.com/foo.phar.asc');

            $pharFile = new File('foo.phar', 'foo');
            $this->fileDownloader->download($url)->willReturn($pharFile);
            $this->fileDownloader->download($signatureUrl)->willReturn(new File('foo.phar.asc', 'bar'));

            $this->signatureService->verify('foo', 'bar')->willReturn(true);

            $this->checksumService->verify($expectedHash, $pharFile)->shouldBeCalled()->willReturn(true);

            $downloader = new PharDownloader($this->fileDownloader->reveal(), $this->signatureService->reveal(), $this->checksumService->reveal());
            $downloader->download($release);
        }

        /**
         * @expectedException \PharIo\Phive\VerificationFailedException
         */
        public function testThrowsExceptionIfSignatureVerificationFails() {
            $url = new Url('https://example.com/foo.phar');
            $expectedHash = new Sha1Hash(sha1('foo'));
            $release = new Release(new Version('1.0.0'), $url, $expectedHash);
            $signatureUrl = new Url('https://example.com/foo.phar.asc');

            $pharFile = new File('foo.phar', 'foo');
            $this->fileDownloader->download($url)->willReturn($pharFile);
            $this->fileDownloader->download($signatureUrl)->willReturn(new File('foo.phar.asc', 'bar'));

            $this->signatureService->verify('foo', 'bar')->willReturn(false);

            $downloader = new PharDownloader($this->fileDownloader->reveal(), $this->signatureService->reveal(), $this->checksumService->reveal());
            $downloader->download($release);
        }

        /**
         * @expectedException \PharIo\Phive\VerificationFailedException
         */
        public function testThrowsExceptionIfChecksumVerificationFails() {
            $url = new Url('https://example.com/foo.phar');
            $expectedHash = new Sha1Hash(sha1('foo'));
            $release = new Release(new Version('1.0.0'), $url, $expectedHash);
            $signatureUrl = new Url('https://example.com/foo.phar.asc');

            $pharFile = new File('foo.phar', 'foo');
            $this->fileDownloader->download($url)->willReturn($pharFile);
            $this->fileDownloader->download($signatureUrl)->willReturn(new File('foo.phar.asc', 'bar'));

            $this->signatureService->verify('foo', 'bar')->willReturn(true);

            $this->checksumService->verify($expectedHash, $pharFile)->shouldBeCalled()->willReturn(false);

            $downloader = new PharDownloader($this->fileDownloader->reveal(), $this->signatureService->reveal(), $this->checksumService->reveal());
            $downloader->download($release);
        }

    }

}

