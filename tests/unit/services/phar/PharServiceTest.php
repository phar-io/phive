<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

/**
 * @covers PharIo\Phive\PharService
 */
class PharServiceTest extends \PHPUnit_Framework_TestCase {

    public function testInstallByUrlDownloadsPharAndInvokesInstaller() {
        $url = new PharUrl('https://example.com/foo-1.20.1.phar');
        $release = new Release('foo', new Version('1.20.1'), $url, null);
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = new RequestedPharUrl($url);

        $expectedPhar = new Phar('foo', new Version('1.20.1'), $file);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn(false);
        $registry->expects($this->once())->method('addPhar');
        $registry->expects($this->once())->method('addUsage')
            ->with($expectedPhar, new Filename('/tmp/foo'));

        $downloader = $this->getPharDownloaderMock();
        $downloader->expects($this->once())->method('download')->with($release)->willReturn($expectedPhar);

        $installer = $this->getPharInstallerMock();
        $installer->method('install')
            ->with($file, new Filename('/tmp/foo'), true);

        $directory = $this->getDirectoryMock();
        $directory->expects($this->once())->method('file')->willReturn(new Filename('/tmp/foo'));

        $service = new PharService(
            $downloader,
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $this->getOutputMock(),
            $this->getSourceRepositoryLoaderMock()
        );

        $service->install($requestedPhar, $directory, true);
    }

    public function testInstallByUrlGetsPharFromRepositoryAndInvokesInstaller() {
        $url = new PharUrl('https://example.com/foo-1.20.1.phar');
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = new RequestedPharUrl($url);

        $phar = new Phar('foo', new Version('1.20.1'), $file);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn(true);
        $registry->method('getPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn($phar);
        $registry->expects($this->once())->method('addUsage')
            ->with($phar, new Filename('/tmp/foo'));

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->once())
            ->method('install')
            ->with($file, new Filename('/tmp/foo'), true);

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('/tmp/foo'));

        $service = new PharService(
            $this->getPharDownloaderMock(),
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $this->getOutputMock(),
            $this->getSourceRepositoryLoaderMock()
        );

        $service->install($requestedPhar, $directory, true);
    }

    public function testUpdate()
    {
        $url = new PharUrl('https://example.com/foo-1.20.1.phar');
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = new RequestedPharUrl($url);

        $phar = new Phar('foo', new Version('1.20.1'), $file);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn(true);
        $registry->method('getPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn($phar);
        $registry->expects($this->once())
            ->method('addUsage')
            ->with($phar, new Filename('/tmp/foo'));

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->once())
            ->method('install')
            ->with($file, new Filename('/tmp/foo'), false);

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('/tmp/foo'));

        $service = new PharService(
            $this->getPharDownloaderMock(),
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $this->getOutputMock(),
            $this->getSourceRepositoryLoaderMock()
        );

        $service->update($requestedPhar, $directory);
    }

    public function testInstallSkipsPharIfAlreadyInstalled()
    {
        $url = new PharUrl('https://example.com/foo-1.20.1.phar');
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = new RequestedPharUrl($url);

        $phar = new Phar('foo', new Version('1.20.1'), $file);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn(true);
        $registry->method('getPhar')
            ->with('foo', new Version('1.20.1'))
            ->willReturn($phar);

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->never())->method('install');

        $output = $this->getOutputMock();
        $output->expects($this->once())
            ->method('writeInfo');

        $filename = $this->getMockWithoutInvokingTheOriginalConstructor(Filename::class);
        $filename->method('exists')->willReturn(true);

        $directory = $this->getDirectoryMock();
        $directory->expects($this->once())->method('file')->willReturn($filename);

        $service = new PharService(
            $this->getPharDownloaderMock(),
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $output,
            $this->getSourceRepositoryLoaderMock()
        );

        $service->install($requestedPhar, $directory, true);
    }

    public function testInstallHandlesDownloadFailedException()
    {
        $url = new PharUrl('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = new RequestedPharUrl($url);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')->willReturn(false);

        $downloader = $this->getPharDownloaderMock();
        $downloader->method('download')->willThrowException(new DownloadFailedException());

        $output = $this->getOutputMock();
        $output->expects($this->never())->method('writeError');

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->never())->method('install');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('/tmp/foo'));

        $this->expectException(DownloadFailedException::class);

        $service = new PharService(
            $downloader,
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $output,
            $this->getSourceRepositoryLoaderMock()
        );

        $service->install($requestedPhar, $directory, false);
    }

    public function testInstallHandlesPharRegistryException()
    {
        $url = new PharUrl('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = new RequestedPharUrl($url);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')->willReturn(true);
        $registry->method('getPhar')->willThrowException(new PharRegistryException());

        $output = $this->getOutputMock();
        $output->expects($this->never())->method('writeError');

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->never())->method('install');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('/tmp/foo'));

        $service = new PharService(
            $this->getPharDownloaderMock(),
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $output,
            $this->getSourceRepositoryLoaderMock()
        );

        $this->expectException(PharRegistryException::class);

        $service->install($requestedPhar, $directory, false);
    }

    public function testInstallHandlesVerificationFailedException()
    {
        $url = new PharUrl('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = new RequestedPharUrl($url);

        $registry = $this->getPharRegistryMock();
        $registry->method('hasPhar')->willReturn(false);

        $downloader = $this->getPharDownloaderMock();
        $downloader->method('download')->willThrowException(new VerificationFailedException());

        $output = $this->getOutputMock();
        $output->expects($this->never())->method('writeError');

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->never())->method('install');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('/tmp/foo'));

        $this->expectException(VerificationFailedException::class);

        $service = new PharService(
            $downloader,
            $installer,
            $registry,
            $this->getAliasResolverServiceMock(),
            $output,
            $this->getSourceRepositoryLoaderMock()
        );

        $service->install($requestedPhar, $directory, false);
    }

    public function testInstallHandlesResolveException()
    {
        $requestedPhar = new RequestedPharAlias(new PharAlias('phpunit', new AnyVersionConstraint()));

        $resolver = $this->getAliasResolverServiceMock();
        $resolver->method('resolve')
            ->willThrowException(new ResolveException());

        $output = $this->getOutputMock();
        $output->expects($this->never())->method('writeError');

        $installer = $this->getPharInstallerMock();
        $installer->expects($this->never())->method('install');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->willReturn(new Filename('/tmp/foo'));
        
        $this->expectException(ResolveException::class);

        $service = new PharService(
            $this->getPharDownloaderMock(),
            $installer,
            $this->getPharRegistryMock(),
            $resolver,
            $output,
            $this->getSourceRepositoryLoaderMock()
        );

        $service->install($requestedPhar, $directory, false);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharDownloader
     */
    private function getPharDownloaderMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PharDownloader::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharInstaller
     */
    private function getPharInstallerMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PharInstaller::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharRegistry
     */
    private function getPharRegistryMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PharRegistry::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AliasResolverService
     */
    private function getAliasResolverServiceMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(AliasResolverService::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Output
     */
    private function getOutputMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Cli\Output::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SourceRepositoryLoader
     */
    private function getSourceRepositoryLoaderMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(SourceRepositoryLoader::class);
    }


}



