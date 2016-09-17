<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Output;

/**
 * @covers PharIo\Phive\FileDownloader
 */
class FileDownloaderTest extends \PHPUnit_Framework_TestCase {

    public function testDownloadThrowsExceptionIfResponseHttpCodeIsNot200() {
        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(500);

        $curl = $this->getCurlMock();
        $curl->method('get')->willThrowException(new HttpException());

        $this->expectException(DownloadFailedException::class);

        $downloader = new FileDownloader($curl, $this->createMock(CacheBackend::class));
        $downloader->download(new Url('https://example.com/foo.phar'));
    }

    public function testDownloadReturnsExpectedFile() {
        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(200);
        $response->method('getBody')->willReturn('bar');

        $curl = $this->getCurlMock();
        $curl->method('get')->willReturn($response);

        $expected = new File(new Filename('foo.phar'), 'bar');

        $downloader = new FileDownloader($curl, $this->createMock(CacheBackend::class));
        $actual = $downloader->download(new Url('https://example.com/foo.phar'));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|HttpResponse
     */
    private function getHttpResponseMock() {
        return $this->createMock(HttpResponse::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Curl
     */
    private function getCurlMock() {
        return $this->createMock(Curl::class);
    }

}
