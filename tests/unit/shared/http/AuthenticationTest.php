<?php declare(strict_types = 1);
namespace unit\shared\http;

use PharIo\Phive\BasicAuthentication;
use PharIo\Phive\BearerAuthentication;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Authentication
 */
class AuthenticationTest extends TestCase {
    public function testCanBeConvertedToString(): void {
        $this->assertEquals(
            'Authorization: Bearer foo',
            (new BearerAuthentication('example.com', 'foo'))->asHttpHeaderString()
        );
    }

    public function testFromLoginPassword(): void {
        // https://tools.ietf.org/html/rfc7617#section-2
        $this->assertEquals(
            'Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
            BasicAuthentication::fromLoginPassword('example.com', 'Aladdin', 'open sesame')->asHttpHeaderString()
        );
    }
}
