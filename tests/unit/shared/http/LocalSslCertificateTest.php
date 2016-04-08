<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\LocalSslCertificate
 */
class LocalSslCertificateTest extends \PHPUnit_Framework_TestCase {

    public function testGetHostname() {
        $certificate = new LocalSslCertificate('example.com', __DIR__ . '/fixtures/foo.pem');
        $this->assertSame('example.com', $certificate->getHostname());
    }

    public function testGetCertificateFileReturnsTemporaryFilename() {
        $certificate = new LocalSslCertificate('example.com', __DIR__ . '/fixtures/foo.pem');
        $actualFilename = $certificate->getCertificateFile();
        $this->assertFileExists($actualFilename);
        $this->assertFileEquals(__DIR__ . '/fixtures/foo.pem', $actualFilename);
    }

}
