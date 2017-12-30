<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\CurlHttpClient
 */
class CurlHttpClientTest extends TestCase {

    /**
     * @var Curl|PHPUnit_Framework_MockObject_MockObject
     */
    private $curl;

    /**
     * @var CurlConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private $curlConfig;

    /**
     * @var HttpProgressHandler|PHPUnit_Framework_MockObject_MockObject
     */
    private $progressHandler;

    /**
     * @var CurlHttpClient
     */
    private $curlHttpClient;

    protected function setUp() {
        $this->curl = $this->getCurlMock();
        $this->curlConfig = $this->getCurlConfigMock();
        $this->curlConfig->method('asCurlOptArray')->willReturn([]);
        $this->progressHandler = $this->getHttpProgressHandlerMock();

        $this->curlHttpClient = new CurlHttpClient($this->curlConfig, $this->progressHandler, $this->curl);
    }


    public function testHeadRequestDisablesProgress() {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('disableProgressMeter');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curlHttpClient->head($url);
    }

    public function testHeadRequestSetsOptionToDisableBody() {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('doNotReturnBody');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curlHttpClient->head($url);
    }

    public function testAddsLocalCertificateFileToCurl() {
        $url = new Url('https://example.com');

        $this->curlConfig->method('hasLocalSslCertificate')
            ->willReturn(true);

        $localCertificate = $this->getLocalSslCertificateMock();
        $localCertificate->method('getCertificateFile')
            ->willReturn('/path/cert.pem');

        $this->curlConfig->method('getLocalSslCertificate')
            ->willReturn($localCertificate);


        $this->curl->expects($this->once())
            ->method('setCertificateFile')
            ->with('/path/cert.pem');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(200);


        $this->curlHttpClient->get($url);
    }

    public function testThrowsHttpExceptionIfHttpCodeIsBetween1And399() {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(302);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(302);

        $this->curlHttpClient->get($url);
    }

    public function testThrowsHttpExceptionIfHttpCodeIs0() {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(0);

        $this->expectException(HttpException::class);

        $this->curlHttpClient->get($url);
    }

    public function testHandleProgressInfoPassesEpectedObjectToProgressHandler() {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->progressHandler->expects($this->once())
            ->method('handleUpdate')
            ->with(new HttpProgressUpdate(
                new Url('https://example.com'),
                1,
                100,
                2,
                0
            ));

        $this->curlHttpClient->head(new Url('https://example.com'));
        $this->curlHttpClient->handleProgressInfo(null, 1, 100, 2, 0);
    }

    public function testHandleProgressInfoReturns0IfHttpCodeIsGreaterThanOrEquals400() {
        $this->curl->method('getHttpCode')
            ->willReturn(400);

        $this->curlHttpClient->head(new Url('https://example.com'));
        $this->assertSame(0, $this->curlHttpClient->handleProgressInfo(null, 1, 100, 2, 0));
    }

    public function testAddsExpectedRateLimitToResponseIfHeadersArePresent() {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $expectedRateLimit = new RateLimit(25, 10, new \DateTimeImmutable('@1514645901'));

        $this->curl->method('exec')
            ->willReturnCallback(function() {
                // simulate header function call, which is normally done by Curl
                $this->curlHttpClient->handleHeaderInput(null, 'X-RateLimit-Limit: 25');
                $this->curlHttpClient->handleHeaderInput(null, 'X-RateLimit-Remaining: 10');
                $this->curlHttpClient->handleHeaderInput(null, 'X-RateLimit-Reset: 1514645901');
            });

        $actualResponse = $this->curlHttpClient->get(new Url('https://example.com'));

        $this->assertTrue($actualResponse->hasRateLimit());
        $this->assertEquals($expectedRateLimit, $actualResponse->getRateLimit());
    }

    public function testAddsRequestHeaderIfEtagIsProvided() {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $etag = new ETag('foo');

        $this->curl->expects($this->once())
            ->method('addHttpHeaders')
            ->with(['If-None-Match: foo']);

        $this->curlHttpClient->get(new Url('https://example.com'), $etag);
    }

    public function testAddsEtagToResponseIfHeaderIsPresent() {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curl->method('exec')
            ->willReturnCallback(function() {
                // simulate header function call, which is normally done by Curl
                $this->curlHttpClient->handleHeaderInput(null, 'etag: foo');
            });

        $expectedETag = new ETag('foo');

        $actualResponse = $this->curlHttpClient->get(new Url('https://example.com'));

        $this->assertTrue($actualResponse->hasETag());
        $this->assertEquals($expectedETag, $actualResponse->getETag());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|CurlConfig
     */
    private function getCurlConfigMock() {
        return $this->createMock(CurlConfig::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Curl
     */
    private function getCurlMock() {
        return $this->createMock(Curl::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|HttpProgressHandler
     */
    private function getHttpProgressHandlerMock() {
        return $this->createMock(HttpProgressHandler::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|LocalSslCertificate
     */
    private function getLocalSslCertificateMock() {
        return $this->createMock(LocalSslCertificate::class);
    }

}
