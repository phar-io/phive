<?php
namespace PharIo\Phive {

    use Prophecy\Argument;
    use Prophecy\Prophecy\ObjectProphecy;

    class GnupgKeyDownloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var Curl|ObjectProphecy
         */
        private $curl;

        /**
         * @var Logger|ObjectProphecy
         */
        private $logger;

        public function setUp() {
            $this->curl = $this->prophesize(Curl::class);
            $this->logger = $this->prophesize(Logger::class);
        }

        public function testInvokesCurlWithExpectedParams() {
            $response = $this->prophesize(CurlResponse::class);
            $response->getHttpCode()->willReturn(200);
            $response->getBody()->willReturn('Some Key');

            $this->curl->get(
                'https://example.com/pks/lookup', ['search' => '0x12345678', 'op' => 'get', 'options' => 'mr']
            )->shouldBeCalled()->willReturn($response->reveal());

            $downloader = new GnupgKeyDownloader(
                $this->curl->reveal(), [new Url('https://example.com')], $this->logger->reveal()
            );
            $downloader->download('12345678');
        }

        public function testReturnsExpectedKey() {
            $response = $this->prophesize(CurlResponse::class);
            $response->getHttpCode()->willReturn(200);
            $response->getBody()->willReturn('Some Key String');

            $this->curl->get(Argument::any(), Argument::any())
                ->willReturn($response->reveal());

            $downloader = new GnupgKeyDownloader(
                $this->curl->reveal(), [new Url('https://example.com')], $this->logger->reveal()
            );
            $this->assertSame('Some Key String', $downloader->download('12345678'));
        }

        /**
         * @expectedException \PharIo\Phive\DownloadFailedException
         */
        public function testThrowsExceptionIfKeyWasNotFound() {
            $response = $this->prophesize(CurlResponse::class);
            $response->getHttpCode()->willReturn(404);
            $response->getErrorMessage()->willReturn('Not Found');

            $this->curl->get(Argument::any(), Argument::any())->willReturn($response);
            $downloader = new GnupgKeyDownloader(
                $this->curl->reveal(), [new Url('https://example.com')], $this->logger->reveal()
            );
            $downloader->download('12345678');
        }

    }

}

