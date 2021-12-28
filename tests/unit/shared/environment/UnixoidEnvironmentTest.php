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

use const STDOUT;
use function function_exists;
use function posix_isatty;
use BadMethodCallException;
use PharIo\FileSystem\Directory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\Environment
 * @covers \PharIo\Phive\UnixoidEnvironment
 */
class UnixoidEnvironmentTest extends TestCase {
    public static function tputCommandDataProvider() {
        return [
            [1, '', false],
            [2, '', false],
            [0, '0', false],
            [0, '7', false],
            [0, '8', true],
            [0, '255', true]
        ];
    }

    public function testReturnsExpectedGlobalBinDir(): void {
        $env = new UnixoidEnvironment([], $this->getExecutorMock());
        $this->assertEquals(new Directory('/usr/local/bin'), $env->getGlobalBinDir());
    }

    /**
     * @dataProvider hasProxyProvider
     *
     * @param bool $expected
     */
    public function testHasProxy(array $server, $expected): void {
        $env = new UnixoidEnvironment($server, $this->getExecutorMock());
        $this->assertSame($expected, $env->hasProxy());
    }

    public function hasProxyProvider() {
        return [
            [[], false],
            [['https_proxy' => 'foo'], true]
        ];
    }

    /**
     * @dataProvider getProxyProvider
     *
     * @param string $proxy
     */
    public function testGetProxy($proxy): void {
        $env = new UnixoidEnvironment(['https_proxy' => $proxy], $this->getExecutorMock());
        $this->assertSame($proxy, $env->getProxy());
    }

    public function getProxyProvider() {
        return [
            ['https://proxy.example.com:8080'],
            ['http://proxy.domain.tld'],
        ];
    }

    public function testGetHomeDirectory(): void {
        $env = new UnixoidEnvironment(['HOME' => __DIR__], $this->getExecutorMock());
        $this->assertSame(__DIR__, $env->getHomeDirectory()->asString());
    }

    public function testGetPhiveHomeVariable(): void {
        $env = new UnixoidEnvironment(['PHIVE_HOME' => __DIR__], $this->getExecutorMock());
        $this->assertSame(__DIR__, $env->getPhiveHomeVariable());
    }

    public function testGetProxyThrowsExceptionIfProxyIsNotSet(): void {
        $env = new UnixoidEnvironment([], $this->getExecutorMock());
        $this->expectException(BadMethodCallException::class);
        $env->getProxy();
    }

    public function testGetHomeDirectoryThrowsExceptionIfHomeIsNotSet(): void {
        $env = new UnixoidEnvironment([], $this->getExecutorMock());
        $this->expectException(BadMethodCallException::class);
        $env->getHomeDirectory();
    }

    /**
     * @dataProvider tputCommandDataProvider
     *
     * @param int    $commandExitCode
     * @param string $commandOutput
     * @param bool   $expectedResult
     */
    public function testSupportsColoredOutput($commandExitCode, $commandOutput, $expectedResult): void {
        if (!function_exists('posix_isatty') || !posix_isatty(STDOUT)) {
            $this->markTestSkipped('requires tty');
        }

        $result   = new ExecutorResult('tput', [$commandOutput], $commandExitCode);
        $executor = $this->getExecutorMock();
        $executor->method('execute')->willReturn($result);

        $env = new UnixoidEnvironment(['TERM' => 'xterm'], $executor);
        $this->assertSame($expectedResult, $env->supportsColoredOutput());
    }

    public function testHasVariable(): void {
        $environment = new UnixoidEnvironment([], $this->getExecutorMock());
        $this->assertFalse($environment->hasVariable('GITHUB_AUTH_TOKEN'));
        $this->assertFalse($environment->hasVariable('FOO'));

        $environment = new UnixoidEnvironment(['FOO' => 'bar'], $this->getExecutorMock());
        $this->assertTrue($environment->hasVariable('FOO'));
    }

    public function testGetVariable(): void {
        $environment = new UnixoidEnvironment(['FOO' => 'bar'], $this->getExecutorMock());
        $this->assertTrue($environment->hasVariable('FOO'));
        $this->assertEquals('bar', $environment->getVariable('FOO'));
    }

    /**
     * @return Executor|PHPUnit_Framework_MockObject_MockObject
     */
    private function getExecutorMock() {
        return $this->createMock(Executor::class);
    }
}
