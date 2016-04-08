<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\LocalSslCertificate
 */
class LocalSslCertificateTest extends \PHPUnit_Framework_TestCase {

    public function testGetHostname() {
        $certificate = new LocalSslCertificate('example.com', 'foo.pem');
        $this->assertSame('example.com', $certificate->getHostname());
    }

    public function testGetCertificateFile() {
        $certificate = new LocalSslCertificate('example.com', 'foo.pem');
        $this->assertSame('foo.pem', $certificate->getCertificateFile());
    }

}
