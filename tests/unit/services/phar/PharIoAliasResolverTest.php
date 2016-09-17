<?php
namespace PharIo\Phive;

/**
 * @covers \PharIo\Phive\PharIoAliasResolver
 */
class PharIoAliasResolverTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var SourcesList
     */
    private $sourcesList;

    /**
     * @var RemoteSourcesListFileLoader
     */
    private $sourcesListFileLoader;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    public function testReturnsRepository() {
        $alias = new PharAlias('phpunit', new AnyVersionConstraint(), new AnyVersionConstraint());
        $url = new Url('https://example.com/bar');
        $source = new Source('phar.io', $url);

        $this->sourcesList = $this->prophesize(SourcesList::class);
        $this->sourcesList->getSourceForAlias($alias)->willReturn($source);

        $this->sourcesListFileLoader = $this->prophesize(RemoteSourcesListFileLoader::class);
        $this->sourcesListFileLoader->load()->shouldBeCalled()->willReturn($this->sourcesList);

        $file = $this->prophesize(File::class);

        $this->fileDownloader = $this->prophesize(FileDownloader::class);
        $this->fileDownloader->download($url)->willReturn($file);

        $resolver = new PharIoAliasResolver(
            $this->sourcesListFileLoader->reveal(),
            $this->fileDownloader->reveal()
        );

        $this->assertInstanceOf(SourceRepository::class, $resolver->resolve($alias));
    }

}



