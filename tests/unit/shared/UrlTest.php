<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\Url
 */
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

    public function testReturnsExpectedHostname() {
        $url = new Url('https://example.com/foo/bar');
        $this->assertSame('example.com', $url->getHostname());
    }
    
}



