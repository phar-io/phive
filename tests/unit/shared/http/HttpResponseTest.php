<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\HttpResponse
 */
class HttpResponseTest extends \PHPUnit_Framework_TestCase {

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

    /**
     * @dataProvider stringProvider
     *
     * @param string $message
     */
    public function testGetErrorMessage($message) {
        $response = new HttpResponse(400, '');
        $this->assertEquals($message, $response->getErrorMessage());
    }

}



