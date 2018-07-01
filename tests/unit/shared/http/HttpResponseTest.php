<?php
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
    public function testGetHttpCode($code) {
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
    public function testGetBody($body) {
        $response = new HttpResponse(200, $body);
        $this->assertEquals($body, $response->getBody());
    }

    public function testHasETagReturnsTrueWhenEtagIsSet() {
        $response = new HttpResponse(200, 'abc', $this->createMock(ETag::class));
        $this->assertTrue($response->hasETag());
    }

    public function testHasETagReturnsFalseWhenNoEtagIsSet() {
        $response = new HttpResponse(200, 'abc');
        $this->assertFalse($response->hasETag());
    }

    public function testGetEtagThrowsExceptionIfNoETagIsAvailable() {
        $response = new HttpResponse(200, 'abc');
        $this->expectException(HttpResponseException::class);
        $response->getETag();
    }

    public function testHETagCanBeRetrieved() {
        $etag = $this->createMock(ETag::class);
        $response = new HttpResponse(200, 'abc', $etag);
        $this->assertEquals($etag, $response->getETag());
    }

}
