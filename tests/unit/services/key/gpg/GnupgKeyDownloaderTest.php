<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GnupgKeyDownloaderTest extends TestCase {

    /**
     * @var CurlHttpClient|ObjectProphecy
     */
    private $curl;

    /**
     * @var Cli\Output|ObjectProphecy
     */
    private $output;

    public function setUp() {
        $this->curl = $this->prophesize(CurlHttpClient::class);
        $this->output = $this->prophesize(Cli\Output::class);
    }

    public function testInvokesCurlWithExpectedParams() {
        $response = $this->prophesize(HttpResponse::class);
        $response->getHttpCode()->willReturn(200);
        $response->getBody()->willReturn('Some PublicKey');
        $response->isNotFound()->willReturn(false);

        $this->curl->get(
            new Url('https://example.com/pks/lookup?search=0x12345678&op=index&options=mr')
        )->shouldBeCalled()->willReturn($response->reveal());

        $this->curl->get(
            new Url('https://example.com/pks/lookup?search=0x12345678&op=get&options=mr')
        )->shouldBeCalled()->willReturn($response->reveal());

        $downloader = new GnupgKeyDownloader(
            $this->curl->reveal(), [new Url('https://example.com')], $this->output->reveal()
        );
        $downloader->download('12345678');
    }

    public function testReturnsExpectedKey() {
        $response = $this->prophesize(HttpResponse::class);
        $response->getHttpCode()->willReturn(200);
        $response->getBody()->willReturn('Some Key Info');
        $response->getBody()->willReturn('Some Public Key Data');
        $response->isNotFound()->willReturn(false);

        $this->curl->get(Argument::any())
            ->willReturn($response->reveal());

        $downloader = new GnupgKeyDownloader(
            $this->curl->reveal(), [new Url('https://example.com')], $this->output->reveal()
        );

        $key = new PublicKey('12345678', 'Some Key Info', 'Some Public Key Data');
        $this->assertEquals($key, $downloader->download('12345678'));
    }

    public function testThrowsExceptionIfKeyWasNotFound() {
        /*
        $response = $this->prophesize(HttpResponse::class);
        $response->getHttpCode()->willReturn(404);
        $response->getErrorMessage()->willReturn('Not Found');
        */
        $this->curl->get(Argument::any())->willThrow(HttpException::class);
        $downloader = new GnupgKeyDownloader(
            $this->curl->reveal(), [new Url('https://example.com')], $this->output->reveal()
        );

        $this->expectException(DownloadFailedException::class);
        $downloader->download('12345678');
    }

}



