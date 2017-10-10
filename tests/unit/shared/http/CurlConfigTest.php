<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\CurlConfig
 */
class CurlConfigTest extends TestCase {

    public function testPutsUserAgentInCurlOptArray() {
        $config = new CurlConfig('Some Agent');
        $actual = $config->asCurlOptArray();
        $this->assertArrayHasKey(CURLOPT_USERAGENT, $actual);
        $this->assertSame('Some Agent', $actual[CURLOPT_USERAGENT]);
    }

    public function testSetsExpectedDefaultsInCurlOptArray() {
        $config = new CurlConfig('foo');
        $expectedDefaults = [
            CURLOPT_MAXREDIRS       => 5,
            CURLOPT_CONNECTTIMEOUT  => 60,
            CURLOPT_SSL_VERIFYHOST  => 2,
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_FAILONERROR     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_LOW_SPEED_TIME  => 90,
            CURLOPT_LOW_SPEED_LIMIT => 128
        ];
        $actual = $config->asCurlOptArray();
        $this->assertArraySubset($expectedDefaults, $actual);
    }

    public function testPutsProxyWithoutCredentialsInCurlOptArray() {
        $config = new CurlConfig('foo');
        $config->setProxy('proxy.example.com');
        $expected = [
            CURLOPT_PROXY => 'proxy.example.com'
        ];
        $actual = $config->asCurlOptArray();
        $this->assertArraySubset($expected, $actual);
    }

    public function testPutsProxyWithCredentialsInCurlOptArray() {
        $config = new CurlConfig('foo');
        $config->setProxy('proxy.example.com', 'someuser', 'somepassword');
        $expected = [
            CURLOPT_PROXY        => 'proxy.example.com',
            CURLOPT_PROXYUSERPWD => 'someuser:somepassword'
        ];
        $actual = $config->asCurlOptArray();
        $this->assertArraySubset($expected, $actual);
    }

    public function testAddsLocalSslCertificate() {
        $config = new CurlConfig('foo');
        $url = 'example.com';
        $certificate = new LocalSslCertificate($url, __DIR__ . '/fixtures/foo.pem');
        $this->assertFalse($config->hasLocalSslCertificate($url));
        $config->addLocalSslCertificate($certificate);
        $this->assertTrue($config->hasLocalSslCertificate($url));
        $this->assertSame($certificate, $config->getLocalSslCertificate($url));
    }

    /**
     * @expectedException \PharIo\Phive\CurlConfigException
     */
    public function testGetLocalSslCertificateThrowsExceptionIfCertificateDoesNotExist() {
        $config = new CurlConfig('foo');
        $config->getLocalSslCertificate('example.com');
    }

}


