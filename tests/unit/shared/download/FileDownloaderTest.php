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
        $curl->method('get')->willReturn($response);

        $this->expectException(DownloadFailedException::class);

        $downloader = new FileDownloader($curl);
        $downloader->download(new Url('https://example.com/foo.phar'));
    }

    public function testDownloadThrowsExceptionIfBodyIsEmpty() {
        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(200);
        $response->method('getBody')->willReturn('');

        $curl = $this->getCurlMock();
        $curl->method('get')->willReturn($response);

        $this->expectException(DownloadFailedException::class);

        $downloader = new FileDownloader($curl);
        $downloader->download(new Url('https://example.com/foo.phar'));
    }

    public function testDownloadReturnsExpectedFile() {
        $response = $this->getHttpResponseMock();
        $response->method('getHttpCode')->willReturn(200);
        $response->method('getBody')->willReturn('bar');

        $curl = $this->getCurlMock();
        $curl->method('get')->willReturn($response);

        $expected = new File(new Filename('foo.phar'), 'bar');

        $downloader = new FileDownloader($curl);
        $actual = $downloader->download(new Url('https://example.com/foo.phar'));

        $this->assertEquals($expected, $actual);
    }

    public function testHandleUpdateReturnsTrueIfUpdateSizeIs0() {
        $update = $this->getHttpProgressUpdateMock();
        $update->method('getExpectedDownloadSize')->willReturn(0);

        $output = $this->getOutputMock();
        $output->expects($this->never())->method('writeInfo');

        $downloader = new FileDownloader($this->getCurlMock());
        $downloader->handleUpdate($update);
    }

    /**
     * @dataProvider httpProgressUpdateProvider
     *
     * @param int    $downloadSize
     * @param int    $bytesReceived
     * @param string $expectedInfo
     */
    public function testHandleUpdateWritesExpectedInfoToOutput($downloadSize, $bytesReceived, $expectedInfo) {
        $update = $this->getHttpProgressUpdateMock();
        $update->method('getExpectedDownloadSize')->willReturn($downloadSize);
        $update->method('getBytesReceived')->willReturn($bytesReceived);
        $update->method('getUrl')->willReturn(new Url('https://example.com/foo.phar'));

        $output = $this->getOutputMock();
        $output->expects($this->once())->method('writeInfo')->willReturnCallback(
            function ($info) use ($expectedInfo) {
                $this->assertContains('https://example.com/foo.phar', $info);
                $this->assertContains($expectedInfo, $info);
            }
        );

        $downloader = new FileDownloader($this->getCurlMock());
        $downloader->handleUpdate($update);
    }

    public static function httpProgressUpdateProvider() {
        return [
            [1000, 512, '512 B / 1000 B'],
            [2048, 512, '0.50 KB / 2.00 KB'],
            [10548576, 46231, '0.04 MB / 10.06 MB'],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|HttpProgressUpdate
     */
    private function getHttpProgressUpdateMock() {
        return $this->createMock(HttpProgressUpdate::class);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Output
     */
    private function getOutputMock() {
        return $this->createMock(Output::class);
    }

}
