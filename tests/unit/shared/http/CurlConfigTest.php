<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\CurlConfig
 */
class CurlConfigTest extends TestCase {
    public function testPutsUserAgentInCurlOptArray(): void {
        $config = new CurlConfig('Some Agent');
        $actual = $config->asCurlOptArray();
        $this->assertArrayHasKey(\CURLOPT_USERAGENT, $actual);
        $this->assertSame('Some Agent', $actual[\CURLOPT_USERAGENT]);
    }

    public function testSetsExpectedDefaultsInCurlOptArray(): void {
        $config           = new CurlConfig('foo');
        $expectedDefaults = [
            \CURLOPT_MAXREDIRS       => 5,
            \CURLOPT_CONNECTTIMEOUT  => 60,
            \CURLOPT_SSL_VERIFYHOST  => 2,
            \CURLOPT_SSL_VERIFYPEER  => true,
            \CURLOPT_FAILONERROR     => false,
            \CURLOPT_RETURNTRANSFER  => true,
            \CURLOPT_FOLLOWLOCATION  => true,
            \CURLOPT_LOW_SPEED_TIME  => 90,
            \CURLOPT_LOW_SPEED_LIMIT => 128
        ];
        $actual = $config->asCurlOptArray();
        foreach($expectedDefaults as $key => $value) {
            $this->assertEquals($value, $actual[$key]);
        }
    }

    public function testPutsProxyWithoutCredentialsInCurlOptArray(): void {
        $proxyHost = 'proxy.example.com';
        $config = new CurlConfig('foo');
        $config->setProxy($proxyHost);
        $actual = $config->asCurlOptArray();
        $this->assertEquals($proxyHost, $actual[\CURLOPT_PROXY]);
    }

    public function testPutsProxyWithCredentialsInCurlOptArray(): void {
        $config = new CurlConfig('foo');
        $config->setProxy('proxy.example.com', 'someuser', 'somepassword');
        $expected = [
            \CURLOPT_PROXY        => 'proxy.example.com',
            \CURLOPT_PROXYUSERPWD => 'someuser:somepassword'
        ];
        $actual = $config->asCurlOptArray();
        foreach($expected as $key => $value) {
            $this->assertEquals($value, $actual[$key]);
        }
    }

    public function testAddsLocalSslCertificate(): void {
        $config      = new CurlConfig('foo');
        $url         = 'example.com';
        $certificate = new LocalSslCertificate($url, __DIR__ . '/fixtures/foo.pem');
        $this->assertFalse($config->hasLocalSslCertificate($url));
        $config->addLocalSslCertificate($certificate);
        $this->assertTrue($config->hasLocalSslCertificate($url));
        $this->assertSame($certificate, $config->getLocalSslCertificate($url));
    }

    public function testGetLocalSslCertificateThrowsExceptionIfCertificateDoesNotExist(): void {
        $config = new CurlConfig('foo');

        $this->expectException(\PharIo\Phive\CurlConfigException::class);

        $config->getLocalSslCertificate('example.com');
    }
}
