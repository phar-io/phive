<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GnupgKeyDownloaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Curl|ObjectProphecy
     */
    private $curl;

    /**
     * @var Cli\Output|ObjectProphecy
     */
    private $output;

    public function setUp() {
        $this->curl = $this->prophesize(Curl::class);
        $this->output = $this->prophesize(Cli\Output::class);
    }

    public function testInvokesCurlWithExpectedParams() {
        $response = $this->prophesize(HttpResponse::class);
        $response->getHttpCode()->willReturn(200);
        $response->getBody()->willReturn('Some PublicKey');

        $this->curl->get(
            'https://example.com/pks/lookup'
        )->shouldBeCalled()->willReturn($response->reveal());

        $this->curl->get(
            'https://example.com/pks/lookup'
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

        $this->curl->get(Argument::any())
            ->willReturn($response->reveal());

        $downloader = new GnupgKeyDownloader(
            $this->curl->reveal(), [new Url('https://example.com')], $this->output->reveal()
        );

        $key = new PublicKey('12345678', 'Some Key Info', 'Some Public Key Data');
        $this->assertEquals($key, $downloader->download('12345678'));
    }

    /**
     * @expectedException \PharIo\Phive\DownloadFailedException
     */
    public function testThrowsExceptionIfKeyWasNotFound() {
        $response = $this->prophesize(HttpResponse::class);
        $response->getHttpCode()->willReturn(404);
        $response->getErrorMessage()->willReturn('Not Found');

        $this->curl->get(Argument::any())->willReturn($response);
        $downloader = new GnupgKeyDownloader(
            $this->curl->reveal(), [new Url('https://example.com')], $this->output->reveal()
        );
        $downloader->download('12345678');
    }

}



