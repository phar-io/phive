<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HttpResponse
 */
class HttpResponseTest extends TestCase {
    use ScalarTestDataProvider;

    /**
     * @dataProvider httpCodeProvider
     *
     * @param int $code
     */
    public function testGetHttpCode($code): void {
        $response = new HttpResponse($code, '');
        $this->assertEquals($code, $response->getHttpCode());
    }

    public function httpCodeProvider() {
        return [
            [200],
            [404],
            [500]
        ];
    }

    /**
     * @dataProvider stringProvider
     *
     * @param string $body
     */
    public function testGetBody($body): void {
        $response = new HttpResponse(200, $body);
        $this->assertEquals($body, $response->getBody());
    }

    public function testHasETagReturnsTrueWhenEtagIsSet(): void {
        $response = new HttpResponse(200, 'abc', $this->createMock(ETag::class));
        $this->assertTrue($response->hasETag());
    }

    public function testHasETagReturnsFalseWhenNoEtagIsSet(): void {
        $response = new HttpResponse(200, 'abc');
        $this->assertFalse($response->hasETag());
    }

    public function testGetEtagThrowsExceptionIfNoETagIsAvailable(): void {
        $response = new HttpResponse(200, 'abc');
        $this->expectException(HttpResponseException::class);
        $response->getETag();
    }

    public function testHETagCanBeRetrieved(): void {
        $etag     = $this->createMock(ETag::class);
        $response = new HttpResponse(200, 'abc', $etag);
        $this->assertEquals($etag, $response->getETag());
    }
}
