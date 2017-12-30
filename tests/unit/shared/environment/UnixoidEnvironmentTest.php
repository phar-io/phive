<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\UnixoidEnvironment
 * @covers \PharIo\Phive\Environment
 */
class UnixoidEnvironmentTest extends TestCase {

    /**
     * @dataProvider hasProxyProvider
     *
     * @param array $server
     * @param bool $expected
     */
    public function testHasProxy(array $server, $expected) {
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
    public function testGetProxy($proxy) {
        $env = new UnixoidEnvironment(['https_proxy' => $proxy], $this->getExecutorMock());
        $this->assertSame($proxy, $env->getProxy());
    }

    public function getProxyProvider() {
        return [
            ['https://proxy.example.com:8080'],
            ['http://proxy.domain.tld'],
        ];
    }

    /**
     *
     */
    public function testGetHomeDirectory() {
        $env = new UnixoidEnvironment(['HOME' => __DIR__], $this->getExecutorMock());
        $this->assertSame(__DIR__, (string)$env->getHomeDirectory());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetProxyThrowsExceptionIfProxyIsNotSet() {
        $env = new UnixoidEnvironment([], $this->getExecutorMock());
        $env->getProxy();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetHomeDirectoryThrowsExceptionIfHomeIsNotSet() {
        $env = new UnixoidEnvironment([], $this->getExecutorMock());
        $env->getHomeDirectory();
    }

    /**
     * @dataProvider tputCommandDataProvider
     *
     * @param int $commandExitCode
     * @param string $commandOutput
     * @param bool $expectedResult
     */
    public function testSupportsColoredOutput($commandExitCode, $commandOutput, $expectedResult) {

        if (!function_exists('posix_isatty') || !posix_isatty(STDOUT)) {
            $this->markTestSkipped('requires tty');
        }

        $result = new ExecutorResult('tput', [$commandOutput], $commandExitCode);
        $executor = $this->getExecutorMock();
        $executor->method('execute')->willReturn($result);

        $env = new UnixoidEnvironment(['TERM' => 'xterm'], $executor);
        $this->assertSame($expectedResult, $env->supportsColoredOutput());
    }

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

    public function testGetGitHubAuthToken() {
        $environment = new UnixoidEnvironment([], $this->getExecutorMock());
        $this->assertFalse($environment->hasGitHubAuthToken());

        $environment = new UnixoidEnvironment(['GITHUB_AUTH_TOKEN' => 'foo'], $this->getExecutorMock());
        $this->assertTrue($environment->hasGitHubAuthToken());
        $this->assertSame('foo', $environment->getGitHubAuthToken());
    }

    public function testGetGitHubAuthTokenThrowsExceptionIfNotPresentInEnvironment() {
        $environment = new UnixoidEnvironment([], $this->getExecutorMock());
        $this->expectException(\BadMethodCallException::class);
        $environment->getGitHubAuthToken();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Executor
     */
    private function getExecutorMock() {
        return $this->createMock(Executor::class);
    }
}
