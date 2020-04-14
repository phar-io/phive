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

    /** @var AuthConfig|PHPUnit_Framework_MockObject_MockObject */
    private $authConfig;

    protected function setUp(): void {
        $this->environment  = $this->getEnvironmentMock();
        $this->phiveVersion = $this->getPhiveVersionMock();
        $this->authConfig   = $this->getAuthConfigMock();
        $this->builder      = new CurlConfigBuilder($this->environment, $this->phiveVersion, $this->authConfig);
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
        $this->authConfig->method('getAuthentication')
            ->with('api.github.com')
            ->willReturn(new Authentication('api.github.com', 'token', 'foo'));
        $this->authConfig->method('hasAuthentication')
            ->with('api.github.com')
            ->willReturn(true);

        $config = $this->builder->build();
        $this->assertTrue($config->hasAuthentication('api.github.com'));
        $this->assertSame('Authorization: token foo', $config->getAuthentication('api.github.com')->asString());
    }

    public function testAddsGitLabAuthToken(): void {
        $this->authConfig->method('getAuthentication')
            ->with('gitlab.com')
            ->willReturn(new Authentication('gitlab.com', 'bearer', 'foo'));
        $this->authConfig->method('hasAuthentication')
            ->with('gitlab.com')
            ->willReturn(true);

        $config = $this->builder->build();
        $this->assertTrue($config->hasAuthentication('gitlab.com'));
        $this->assertSame('Authorization: bearer foo', $config->getAuthentication('gitlab.com')->asString());
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

    /**
     * @return AuthConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private function getAuthConfigMock() {
        return $this->createMock(AuthConfig::class);
    }
}
