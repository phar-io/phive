<?php
namespace PharIo\Phive {

    class UrlTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider invalidUriProvider
         * @expectedException \InvalidArgumentException
         *
         * @param string $invalidUri
         */
        public function testThrowsExceptionIfProtocolIsNotHttps($invalidUri) {
            new Url($invalidUri);
        }

        public function invalidUriProvider() {
            return [
                ['http://example.com'],
                ['ftp://example.com'],
                ['example.com']
            ];
        }

        public function testCanBeCastToString() {
            $url = new Url('https://example.com');
            $this->assertSame('https://example.com', (string)$url);
        }
    }

}

