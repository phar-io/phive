<?php
namespace TheSeer\Phive {

    use Prophecy\Argument;
    use Prophecy\Prophecy\ObjectProphecy;

    class GnupgKeyDownloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var Curl|ObjectProphecy
         */
        private $curl;

        public function setUp() {
            $this->curl = $this->prophesize(Curl::class);
        }

        public function testInvokesCurlWithExpectedParams() {
            $this->curl->get(
                'https://example.com/pks/lookup', ['search' => '0x12345678', 'op' => 'get', 'options' => 'mr']
            )->shouldBeCalled();

            $downloader = new GnupgKeyDownloader($this->curl->reveal(), [new Url('https://example.com')]);
            $downloader->download('12345678');
        }

        public function testReturnsExpectedKey() {
            $this->curl->get(Argument::any(), Argument::any())
                ->willReturn('Some Key String');

            $downloader = new GnupgKeyDownloader($this->curl->reveal(), [new Url('https://example.com')]);
            $this->assertSame('Some Key String', $downloader->download('12345678'));
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testThrowsExceptionIfKeyWasNotFound() {
            $this->curl->get(Argument::any(), Argument::any())->willThrow(new CurlException());
            $downloader = new GnupgKeyDownloader($this->curl->reveal(), [new Url('https://example.com')]);
            $downloader->download('12345678');
        }

    }

}

