<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Version\AnyVersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\PharIoAliasResolver
 */
class PharIoAliasResolverTest extends TestCase {
    public function testReturnsRepository(): void {
        $alias         = new PharAlias('phpunit', new AnyVersionConstraint(), new AnyVersionConstraint());
        $requestedPhar = new RequestedPhar(
            $alias,
            new AnyVersionConstraint(),
            new AnyVersionConstraint()
        );
        $url    = new Url('https://example.com/bar');
        $source = new Source('phar.io', $url);

        $sourcesList = $this->createMock(SourcesList::class);
        $sourcesList->method('getSourceForAlias')->with($alias)->willReturn($source);

        $sourcesListFileLoader = $this->createMock(RemoteSourcesListFileLoader::class);
        $sourcesListFileLoader->expects($this->once())->method('load')->willReturn($sourcesList);

        $filename = $this->createMock(Filename::class);
        $filename->method('delete')->willReturn(true);

        $file = $this->createMock(File::class);
        $file->method('getFilename')->willReturn($filename);
        $file->expects($this->once())->method('saveAs');

        $fileDownloader = $this->createMock(FileDownloader::class);
        $fileDownloader->method('download')->with($url)->willReturn($file);

        $resolver = new PharIoAliasResolver(
            $sourcesListFileLoader,
            $fileDownloader
        );

        $this->assertInstanceOf(SourceRepository::class, $resolver->resolve($requestedPhar));
    }
}
