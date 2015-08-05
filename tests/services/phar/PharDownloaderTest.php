<?php
namespace PharIo\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

    class PharDownloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var Curl|ObjectProphecy
         */
        private $curl;

        /**
         * @var SignatureService|ObjectProphecy
         */
        private $signatureService;

        public function setUp() {
            $this->curl = $this->prophesize(Curl::class);
            $this->signatureService = $this->prophesize(SignatureService::class);
        }

        /**
         * @dataProvider httpStatusCodeProvider
         * @expectedException \PharIo\Phive\DownloadFailedException
         *
         * @param int $statusCode
         */
        public function testThrowsExceptionIfHttpStatusCodeIsNot200($statusCode) {
            $url = new Url('https://example.com/foo.phar');
            $curlReponse = new CurlResponse('', ['http_code' => $statusCode], '');
            $this->curl->get($url)->willReturn($curlReponse);
            $downloader = new PharDownloader($this->curl->reveal(), $this->signatureService->reveal());
            $downloader->download($url);
        }

        /**
         * @return array
         */
        public function httpStatusCodeProvider() {
            return [
                [301],
                [403],
                [400],
                [500]
            ];
        }

        public function testReturnsExpectedPharFileIfStatusCodeIs200() {
            $url = new Url('https://example.com/foo.phar');
            $signatureUrl = new Url('https://example.com/foo.phar.asc');
            $curlReponse = new CurlResponse('foo', ['http_code' => 200], '');
            $this->curl->get($url)->willReturn($curlReponse);
            $this->curl->get($signatureUrl)->willReturn($curlReponse);

            $this->signatureService->verify('foo', 'foo')->willReturn(true);

            $expected = new File('foo.phar', 'foo');

            $downloader = new PharDownloader($this->curl->reveal(), $this->signatureService->reveal());
            $this->assertEquals($expected, $downloader->download($url));

        }

    }

}

