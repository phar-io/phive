<?php declare(strict_types = 1);
namespace unit\shared\http;

use PharIo\Phive\Authentication;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Authentication
 */
class AuthenticationTest extends TestCase {
    public function testCanBeConvertedToString(): void {
        $this->assertEquals(
            'Authorization: Bearer foo',
            (new Authentication('example.com', 'Bearer', 'foo'))->asString()
        );
    }

    public function testFromLoginPassword(): void {
        // https://tools.ietf.org/html/rfc7617#section-2
        $this->assertEquals(
            'Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
            Authentication::fromLoginPassword('example.com', 'Aladdin', 'open sesame')->asString()
        );
    }
}
