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
            (new BearerAuthentication('foo'))->asHttpHeaderString()
        );
    }

    public function testFromLoginPassword(): void {
        // https://tools.ietf.org/html/rfc7617#section-2
        $this->assertEquals(
            'Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
            BasicAuthentication::fromLoginPassword('Aladdin', 'open sesame')->asHttpHeaderString()
        );
    }
}
