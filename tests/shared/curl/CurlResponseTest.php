<?php
namespace TheSeer\Phive {

    class CurlResponseTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider httpCodeProvider
         *
         * @param int $code
         */
        public function testGetHttpCode($code) {
            $response = new CurlResponse('', ['http_code' => $code], '');
            $this->assertEquals($code, $response->getHttpCode());
        }

        public function httpCodeProvider() {
            return [
                [200],
                [404],
                [500]
            ];
        }

        public function testHasError() {
            $response = new CurlResponse('', ['http_code' => 200], '');
            $this->assertFalse($response->hasError());

            $response = new CurlResponse('', ['http_code' => 404], 'some error');
            $this->assertTrue($response->hasError());
        }

        /**
         * @dataProvider bodyProvider
         *
         * @param string $body
         */
        public function testGetBody($body) {
            $response = new CurlResponse($body, ['http_code' => 200], '');
            $this->assertEquals($body, $response->getBody());
        }

        public function bodyProvider() {
            return [
                ['foo'],
                ['bar']
            ];
        }

        /**
         * @dataProvider errorMessageProvider
         *
         * @param string $message
         */
        public function testGetErrorMessage($message) {
            $response = new CurlResponse('', ['http_code' => 400], $message);
            $this->assertEquals($message, $response->getErrorMessage());
        }

        public function errorMessageProvider() {
            return [
                ['foo'],
                ['bar']
            ];
        }

    }

}

