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
