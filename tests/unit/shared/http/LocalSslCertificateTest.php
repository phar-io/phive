<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\LocalSslCertificate
 */
class LocalSslCertificateTest extends TestCase {
    public function testGetHostname(): void {
        $certificate = new LocalSslCertificate('example.com', __DIR__ . '/fixtures/foo.pem');
        $this->assertSame('example.com', $certificate->getHostname());
    }

    public function testGetCertificateFileReturnsTemporaryFilename(): void {
        $certificate    = new LocalSslCertificate('example.com', __DIR__ . '/fixtures/foo.pem');
        $actualFilename = $certificate->getCertificateFile();
        $this->assertFileExists($actualFilename);
        $this->assertFileEquals(__DIR__ . '/fixtures/foo.pem', $actualFilename);
    }
}
