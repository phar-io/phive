<?php
namespace TheSeer\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

    class PharDownloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var Curl|ObjectProphecy
         */
        private $curl;

        public function setUp() {
            $this->curl = $this->prophesize(Curl::class);
        }

        /**
         * @dataProvider httpStatusCodeProvider
         * @expectedException \TheSeer\Phive\DownloadFailedException
         *
         * @param int $statusCode
         */
        public function testThrowsExceptionIfHttpStatusCodeIsNot200($statusCode) {
            $url = new Url('https://example.com/foo.phar');
            $curlReponse = new CurlResponse('', ['http_code' => $statusCode], '');
            $this->curl->get($url)->willReturn($curlReponse);
            $downloader = new PharDownloader($this->curl->reveal());
            $downloader->getFile($url);
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
            $curlReponse = new CurlResponse('foo', ['http_code' => 200], '');
            $this->curl->get($url)->willReturn($curlReponse);

            $expected = new PharFile('foo.phar', 'foo');

            $downloader = new PharDownloader($this->curl->reveal());
            $this->assertEquals($expected, $downloader->getFile($url));

        }

    }

}

