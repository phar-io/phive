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

use DateTimeImmutable;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\RemoteSourcesListFileLoader
 */
class RemoteSourcesListFileLoaderTest extends TestCase {
    public function testInvokesDownloaderIfLocalFileDoesNotExist(): void {
        $url      = $this->getUrlMock();
        $filename = $this->getFilenameMock();
        $filename->method('exists')->willReturn(false);
        $downloader = $this->getFileDownloaderMock();

        $loader = new RemoteSourcesListFileLoader(
            $url,
            $filename,
            $downloader,
            $this->getOutputMock(),
            new DateTimeImmutable('25.04.2017 22:58:11')
        );

        $file = $this->getFileMock();

        $downloader->expects($this->once())
            ->method('download')
            ->with($url)
            ->willReturn($file);

        $loader->load();
    }

    public function testInvokesDownloaderIfLocalFileIsOutdated(): void {
        $url      = $this->getUrlMock();
        $filename = $this->getFilenameMock();
        $filename->method('exists')->willReturn(true);
        $filename->method('isOlderThan')->willReturn(true);
        $downloader = $this->getFileDownloaderMock();

        $loader = new RemoteSourcesListFileLoader(
            $url,
            $filename,
            $downloader,
            $this->getOutputMock(),
            new DateTimeImmutable('25.04.2017 22:58:11')
        );

        $file = $this->getFileMock();

        $downloader->expects($this->once())
            ->method('download')
            ->with($url)
            ->willReturn($file);

        $loader->load();
    }

    public function testDoesNotInvokeDownloaderIfLocalFileExistsAndIsNotOutdated(): void {
        $url      = $this->getUrlMock();
        $filename = $this->getFilenameMock();
        $filename->method('exists')->willReturn(true);
        $filename->method('isOlderThan')->willReturn(false);
        $downloader = $this->getFileDownloaderMock();

        $loader = new RemoteSourcesListFileLoader(
            $url,
            $filename,
            $downloader,
            $this->getOutputMock(),
            new DateTimeImmutable('25.04.2017 22:58:11')
        );

        $downloader->expects($this->never())
            ->method('download');

        $loader->load();
    }

    /**
     * @return File|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileMock() {
        return $this->createMock(File::class);
    }

    /**
     * @return Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputMock() {
        return $this->createMock(Output::class);
    }

    /**
     * @return Filename|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Url
     */
    private function getUrlMock() {
        return $this->createMock(Url::class);
    }

    /**
     * @return FileDownloader|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileDownloaderMock() {
        return $this->createMock(FileDownloader::class);
    }
}
