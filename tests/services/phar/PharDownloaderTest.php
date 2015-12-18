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

        public function setUp() {
            $this->fileDownloader = $this->prophesize(FileDownloader::class);
            $this->signatureService = $this->prophesize(SignatureService::class);
        }

        public function testReturnsExpectedPharFileIfStatusCodeIs200() {
            $url = new Url('https://example.com/foo.phar');
            $release = new Release(new Version('1.0.0'), $url, null);
            $signatureUrl = new Url('https://example.com/foo.phar.asc');
            $this->fileDownloader->download($url)->willReturn(new File('foo.phar', 'foo'));
            $this->fileDownloader->download($signatureUrl)->willReturn(new File('foo.phar.asc', 'bar'));

            $this->signatureService->verify('foo', 'bar')->willReturn(true);

            $expected = new File('foo.phar', 'foo');

            $downloader = new PharDownloader($this->fileDownloader->reveal(), $this->signatureService->reveal());
            $this->assertEquals($expected, $downloader->download($release));
        }

    }

}

