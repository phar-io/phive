<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\CurlConfigBuilder
 */
class CurlConfigBuilderTest extends TestCase {
    /** @var Environment|PHPUnit_Framework_MockObject_MockObject */
    private $environment;

    /** @var PhiveVersion|PHPUnit_Framework_MockObject_MockObject */
    private $phiveVersion;

    /** @var CurlConfigBuilder */
    private $builder;

    protected function setUp(): void {
        $this->environment  = $this->getEnvironmentMock();
        $this->phiveVersion = $this->getPhiveVersionMock();
        $this->builder      = new CurlConfigBuilder($this->environment, $this->phiveVersion);
    }

    public function testSetsExpectedUserAgent(): void {
        $this->phiveVersion->method('getVersion')
            ->willReturn('0.8.3');
        $this->environment->method('getRuntimeString')
            ->willReturn('PHP 7.1.11');

        $config = $this->builder->build();
        $this->assertSame('Phive 0.8.3 on PHP 7.1.11', $config->asCurlOptArray()[\CURLOPT_USERAGENT]);
    }

    public function testSetsProxyIfConfiguredInEnvironment(): void {
        $this->environment->method('hasProxy')
            ->willReturn(true);
        $this->environment->method('getProxy')
            ->willReturn('proxy.example.com');

        $config = $this->builder->build();

        $this->assertSame('proxy.example.com', $config->asCurlOptArray()[\CURLOPT_PROXY]);
    }

    public function testAddsGitHubAuthToken(): void {
        $this->environment->method('hasGitHubAuthToken')
            ->willReturn(true);
        $this->environment->method('getGitHubAuthToken')
            ->willReturn('foo');

        $config = $this->builder->build();
        $this->assertTrue($config->hasAuthenticationToken('api.github.com'));
        $this->assertSame('foo', $config->getAuthenticationToken('api.github.com'));
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return PhiveVersion|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPhiveVersionMock() {
        return $this->createMock(PhiveVersion::class);
    }
}
