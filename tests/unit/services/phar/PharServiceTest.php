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

use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PharService
 */
class PharServiceTest extends TestCase {
    public function testReturnsPharFromRegistryIfItExists(): void {
        $phar    = $this->getPharMock();
        $name    = 'some Phar';
        $version = new Version('1.0.0');

        $release = $this->getReleaseMock();
        $release->method('getName')->willReturn($name);
        $release->method('getVersion')->willReturn($version);

        $registry = $this->getPharRegistryMock();
        $registry->expects($this->once())
            ->method('hasPhar')
            ->with($name, $version)
            ->willReturn(true);
        $registry->expects($this->once())
            ->method('getPhar')
            ->with($name, $version)
            ->willReturn($phar);

        $downloader = $this->getPharDownloaderMock();

        $downloader->expects($this->never())
            ->method('download');

        $service = new PharService($registry, $downloader);
        $this->assertSame($phar, $service->getPharFromRelease($release));
    }

    public function testDownloadsPharIfNotPresentInRegistry(): void {
        $phar    = $this->getPharMock();
        $name    = 'some Phar';
        $version = new Version('1.0.0');

        $release = $this->getReleaseMock();
        $release->method('getName')->willReturn($name);
        $release->method('getVersion')->willReturn($version);

        $registry = $this->getPharRegistryMock();
        $registry->expects($this->once())
            ->method('hasPhar')
            ->with($name, $version)
            ->willReturn(false);

        $registry->expects($this->once())
            ->method('addPhar')
            ->with($phar)
            ->willReturn($phar);

        $downloader = $this->getPharDownloaderMock();

        $downloader->expects($this->once())
            ->method('download')
            ->with($release)
            ->willReturn($phar);

        $service = new PharService($registry, $downloader);
        $this->assertSame($phar, $service->getPharFromRelease($release));
    }

    /**
     * @return Phar|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharMock() {
        return $this->createMock(Phar::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|SupportedRelease
     */
    private function getReleaseMock() {
        return $this->createMock(SupportedRelease::class);
    }

    /**
     * @return PharRegistry|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharRegistryMock() {
        return $this->createMock(PharRegistry::class);
    }

    /**
     * @return PharDownloader|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharDownloaderMock() {
        return $this->createMock(PharDownloader::class);
    }
}
