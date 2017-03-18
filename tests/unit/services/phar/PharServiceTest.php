<?php
namespace PharIo\Phive;

use PharIo\Version\Version;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PharService
 */
class PharServiceTest extends \PHPUnit_Framework_TestCase {
    public function testReturnsPharFromRegistryIfItExists() {
        $phar = $this->getPharMock();
        $name = 'some Phar';
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

    public function testDownloadsPharIfNotPresentInRegistry()
    {
        $phar = $this->getPharMock();
        $name = 'some Phar';
        $version = new Version('1.0.0');

        $release = $this->getReleaseMock();
        $release->method('getName')->willReturn($name);
        $release->method('getVersion')->willReturn($version);

        $registry = $this->getPharRegistryMock();
        $registry->expects($this->once())
            ->method('hasPhar')
            ->with($name, $version)
            ->willReturn(false);

        $downloader = $this->getPharDownloaderMock();

        $downloader->expects($this->once())
            ->method('download')
            ->with($release)
            ->willReturn($phar);

        $service = new PharService($registry, $downloader);
        $this->assertSame($phar, $service->getPharFromRelease($release));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Phar
     */
    private function getPharMock()
    {
        return $this->createMock(Phar::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Release
     */
    private function getReleaseMock()
    {
        return $this->createMock(Release::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|PharRegistry
     */
    private function getPharRegistryMock()
    {
        return $this->createMock(PharRegistry::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|PharDownloader
     */
    private function getPharDownloaderMock()
    {
        return $this->createMock(PharDownloader::class);
    }
}
