<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\CurlHttpClient
 */
class CurlHttpClientTest extends TestCase {
    /** @var Curl|PHPUnit_Framework_MockObject_MockObject */
    private $curl;

    /** @var CurlConfig|PHPUnit_Framework_MockObject_MockObject */
    private $curlConfig;

    /** @var HttpProgressHandler|PHPUnit_Framework_MockObject_MockObject */
    private $progressHandler;

    /** @var CurlHttpClient */
    private $curlHttpClient;

    protected function setUp(): void {
        $this->curl       = $this->getCurlMock();
        $this->curlConfig = $this->getCurlConfigMock();
        $this->curlConfig->method('asCurlOptArray')->willReturn([]);
        $this->progressHandler = $this->getHttpProgressHandlerMock();

        $this->curlHttpClient = new CurlHttpClient($this->curlConfig, $this->progressHandler, $this->curl);
    }

    public function testHeadRequestDisablesProgress(): void {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('disableProgressMeter');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curlHttpClient->head($url);
    }

    public function testHeadRequestSetsOptionToDisableBody(): void {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('doNotReturnBody');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curlHttpClient->head($url);
    }

    public function testAddsLocalCertificateFileToCurl(): void {
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

    public function testThrowsHttpExceptionIfHttpCodeIsBetween1And399(): void {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(302);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(302);

        $this->curlHttpClient->get($url);
    }

    public function testThrowsHttpExceptionIfHttpCodeIs0(): void {
        $url = new Url('https://example.com');

        $this->curl->expects($this->once())
            ->method('exec');

        $this->curl->method('getHttpCode')
            ->willReturn(0);

        $this->expectException(HttpException::class);

        $this->curlHttpClient->get($url);
    }

    public function testHandleProgressInfoPassesExpectedObjectToProgressHandler(): void {
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

    public function testHandleProgressInfoReturns0IfHttpCodeIsGreaterThanOrEquals400(): void {
        $this->curl->method('getHttpCode')
            ->willReturn(400);

        $this->curlHttpClient->head(new Url('https://example.com'));
        $this->assertSame(0, $this->curlHttpClient->handleProgressInfo(null, 1, 100, 2, 0));
    }

    public function testAddsExpectedRateLimitToResponseIfHeadersArePresent(): void {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $expectedRateLimit = new RateLimit(25, 10, new DateTimeImmutable('@1514645901'));

        $this->curl->method('exec')
            ->willReturnCallback(function (): string {
                // simulate header function call, which is normally done by Curl
                $this->curlHttpClient->handleHeaderInput(null, 'X-RateLimit-Limit: 25');
                $this->curlHttpClient->handleHeaderInput(null, 'X-RateLimit-Remaining: 10');
                $this->curlHttpClient->handleHeaderInput(null, 'X-RateLimit-Reset: 1514645901');

                return '';
            });

        $actualResponse = $this->curlHttpClient->get(new Url('https://example.com'));

        $this->assertTrue($actualResponse->hasRateLimit());
        $this->assertEquals($expectedRateLimit, $actualResponse->getRateLimit());
    }

    public function testAddsRequestHeaderIfEtagIsProvided(): void {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $etag = new ETag('foo');

        $this->curl->expects($this->once())
            ->method('addHttpHeaders')
            ->with(['If-None-Match: foo']);

        $this->curlHttpClient->get(new Url('https://example.com'), $etag);
    }

    public function testAddsEtagToResponseIfHeaderIsPresent(): void {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curl->method('exec')
            ->willReturnCallback(function (): string {
                // simulate header function call, which is normally done by Curl
                $this->curlHttpClient->handleHeaderInput(null, 'etag: foo');

                return '';
            });

        $expectedETag = new ETag('foo');

        $actualResponse = $this->curlHttpClient->get(new Url('https://example.com'));

        $this->assertTrue($actualResponse->hasETag());
        $this->assertEquals($expectedETag, $actualResponse->getETag());
    }

    public function testAddsAuthorizationHeaderIfAuthIsProvided(): void {
        $this->curl->method('getHttpCode')
            ->willReturn(200);

        $this->curlConfig->method('hasAuthentication')
            ->with('example.com')
            ->willReturn(true);

        $this->curlConfig->method('getAuthentication')
            ->with('example.com')
            ->willReturn(new TokenAuthentication('foobar'));

        $this->curl->expects($this->once())
            ->method('addHttpHeaders')
            ->with(['Authorization: Token foobar']);

        $this->curlHttpClient->get(new Url('https://example.com'));
    }

    /**
     * @return CurlConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private function getCurlConfigMock() {
        return $this->createMock(CurlConfig::class);
    }

    /**
     * @return Curl|PHPUnit_Framework_MockObject_MockObject
     */
    private function getCurlMock() {
        return $this->createMock(Curl::class);
    }

    /**
     * @return HttpProgressHandler|PHPUnit_Framework_MockObject_MockObject
     */
    private function getHttpProgressHandlerMock() {
        return $this->createMock(HttpProgressHandler::class);
    }

    /**
     * @return LocalSslCertificate|PHPUnit_Framework_MockObject_MockObject
     */
    private function getLocalSslCertificateMock() {
        return $this->createMock(LocalSslCertificate::class);
    }
}
