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

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\EnvironmentAuthConfig
 */
class EnvironmentAuthConfigTest extends TestCase {
    public function testHasAuthentication(): void {
        $environment = $this->createMock(Environment::class);
        $environment->method('hasVariable')
            ->withConsecutive(['GITHUB_AUTH_TOKEN'], ['GITLAB_AUTH_TOKEN'])
            ->willReturn(true, false);

        $authConfig = new EnvironmentAuthConfig($environment);

        $this->assertFalse($authConfig->hasAuthentication('example.com'));
        $this->assertTrue($authConfig->hasAuthentication('api.github.com'));
        $this->assertFalse($authConfig->hasAuthentication('gitlab.com'));
    }

    public function testGetAuthentication(): void {
        $environment = $this->createMock(Environment::class);
        $environment->method('hasVariable')->with('GITHUB_AUTH_TOKEN')->willReturn(true);
        $environment->method('getVariable')->with('GITHUB_AUTH_TOKEN')->willReturn('foo');

        $authConfig = new EnvironmentAuthConfig($environment);

        $this->assertEquals(
            'Authorization: Token foo',
            $authConfig->getAuthentication('api.github.com')->asHttpHeaderString()
        );

        $environment = $this->createMock(Environment::class);
        $environment->method('hasVariable')->with('GITLAB_AUTH_TOKEN')->willReturn(true);
        $environment->method('getVariable')->with('GITLAB_AUTH_TOKEN')->willReturn('bar');

        $authConfig = new EnvironmentAuthConfig($environment);

        $this->assertEquals(
            'Authorization: Bearer bar',
            $authConfig->getAuthentication('gitlab.com')->asHttpHeaderString()
        );

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('No authentication data for example.com');

        $authConfig->getAuthentication('example.com');
    }
}
