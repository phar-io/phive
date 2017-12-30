<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\FileDownloader
 */
class FileDownloaderTest extends TestCase {

    public function testDownloadThrowsExceptionIfResponseHttpCodeIsNot200() {
        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(500);

        $curl = $this->getCurlMock();
        $curl->method('get')->willThrowException(new HttpException());

        $this->expectException(DownloadFailedException::class);

        $downloader = new FileDownloader($curl, $this->getCacheBackendMock());
        $downloader->download(new Url('https://example.com/foo.phar'));
    }

    public function testDownloadReturnsExpectedFile() {
        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(200);
        $response->method('getBody')->willReturn('bar');
        $response->method('isSuccess')->willReturn(true);

        $curl = $this->getCurlMock();
        $curl->method('get')->willReturn($response);

        $expected = new File(new Filename('foo.phar'), 'bar');

        $downloader = new FileDownloader($curl, $this->getCacheBackendMock());
        $actual = $downloader->download(new Url('https://example.com/foo.phar'));

        $this->assertEquals($expected, $actual);
    }

    public function testResponseWithETagWillBeStoredInCache() {
        $url = new Url('https://example.com/foo.phar');
        $etag = new ETag('abc');

        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(200);
        $response->method('getBody')->willReturn('bar');
        $response->method('hasETag')->willReturn(true);
        $response->method('getETag')->willReturn($etag);
        $response->method('isSuccess')->willReturn(true);

        $curl = $this->getCurlMock();
        $curl->method('get')->willReturn($response);

        $cache = $this->getCacheBackendMock();
        $cache->expects($this->once())->method('storeEntry')->with(
            $url, $etag, 'bar'
        );

        $downloader = new FileDownloader($curl, $cache);
        $downloader->download($url);
    }

    public function testNotModifiedReturnsContentFromCache() {
        $url = new Url('https://example.com/foo.phar');

        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(304);
        $response->method('isSuccess')->willReturn(true);

        $curl = $this->getCurlMock();
        $curl->method('get')->willReturn($response);


        $cache = $this->getCacheBackendMock();
        $cache->method('getContent')->with($url)->willReturn('bar');

        $downloader = new FileDownloader($curl, $cache);
        $downloader->download($url);

        $actual = $downloader->download($url);
        $expected = new File(new Filename('foo.phar'), 'bar');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|HttpResponse
     */
    private function getHttpResponseMock() {
        return $this->createMock(HttpResponse::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CurlHttpClient
     */
    private function getCurlMock() {
        return $this->createMock(CurlHttpClient::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CacheBackend
     */
    private function getCacheBackendMock()
    {
        return $this->createMock(CacheBackend::class);
    }

}
