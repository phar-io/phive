<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\RemoteSourcesListFileLoader
 */
class RemoteSourcesListFileLoaderTest extends TestCase {
    public function testInvokesDownloaderIfLocalFileDoesNotExist() {
        $url = $this->getUrlMock();
        $filename = $this->getFilenameMock();
        $filename->method('exists')->willReturn(false);
        $downloader = $this->getFileDownloaderMock();

        $loader = new RemoteSourcesListFileLoader(
            $url,
            $filename,
            $downloader,
            $this->getOutputMock(),
            new \DateTimeImmutable('25.04.2017 22:58:11')
        );

        $file = $this->getFileMock();

        $downloader->expects($this->once())
            ->method('download')
            ->with($url)
            ->willReturn($file);

        $loader->load();
    }


    public function testInvokesDownloaderIfLocalFileIsOutdated() {
        $url = $this->getUrlMock();
        $filename = $this->getFilenameMock();
        $filename->method('exists')->willReturn(true);
        $filename->method('isOlderThan')->willReturn(true);
        $downloader = $this->getFileDownloaderMock();

        $loader = new RemoteSourcesListFileLoader(
            $url,
            $filename,
            $downloader,
            $this->getOutputMock(),
            new \DateTimeImmutable('25.04.2017 22:58:11')
        );

        $file = $this->getFileMock();

        $downloader->expects($this->once())
            ->method('download')
            ->with($url)
            ->willReturn($file);

        $loader->load();
    }

    public function testDoesNotInvokeDownloaderIfLocalFileExistsAndIsNotOutdated() {
        $url = $this->getUrlMock();
        $filename = $this->getFilenameMock();
        $filename->method('exists')->willReturn(true);
        $filename->method('isOlderThan')->willReturn(false);
        $downloader = $this->getFileDownloaderMock();

        $loader = new RemoteSourcesListFileLoader(
            $url,
            $filename,
            $downloader,
            $this->getOutputMock(),
            new \DateTimeImmutable('25.04.2017 22:58:11')
        );

        $downloader->expects($this->never())
            ->method('download');

        $loader->load();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|File
     */
    private function getFileMock()
    {
        return $this->createMock(File::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Output
     */
    private function getOutputMock()
    {
        return $this->createMock(Output::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filename
     */
    private function getFilenameMock()
    {
        return $this->createMock(Filename::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Url
     */
    private function getUrlMock()
    {
        return $this->createMock(Url::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FileDownloader
     */
    private function getFileDownloaderMock()
    {
        return $this->createMock(FileDownloader::class);
    }

}
