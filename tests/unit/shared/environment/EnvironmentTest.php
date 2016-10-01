<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\UnixoidEnvironment
 * @covers PharIo\Phive\Environment
 */
class UnixoidEnvironmentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider hasProxyProvider
     *
     * @param string $server
     * @param bool   $expected
     */
    public function testHasProxy($server, $expected) {
        $env = new UnixoidEnvironment($server);
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
        $env = new UnixoidEnvironment(['https_proxy' => $proxy]);
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
        $env = new UnixoidEnvironment(['HOME' => __DIR__]);
        $this->assertSame(__DIR__, (string)$env->getHomeDirectory());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetProxyThrowsExceptionIfProxyIsNotSet() {
        $env = new UnixoidEnvironment([]);
        $env->getProxy();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetHomeDirectoryThrowsExceptionIfHomeIsNotSet() {
        $env = new UnixoidEnvironment([]);
        $env->getHomeDirectory();
    }

    public function testSupportsColoredOutput() {

        $env = new UnixoidEnvironment([]);
        $this->markTestIncomplete( 'Under construction' );
    }
}