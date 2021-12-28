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

use PharIo\FileSystem\File;
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

        $file = $this->createMock(File::class);
        $file->method('getContent')->willReturn('<?xml version="1.0"?><root xmlns="a:b" />');

        $fileDownloader = $this->createMock(FileDownloader::class);
        $fileDownloader->method('download')->with($url)->willReturn($file);

        $resolver = new PharIoAliasResolver(
            $sourcesListFileLoader,
            $fileDownloader
        );

        $this->assertInstanceOf(SourceRepository::class, $resolver->resolve($requestedPhar));
    }
}
