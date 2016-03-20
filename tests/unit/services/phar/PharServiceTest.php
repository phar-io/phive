<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers PharIo\Phive\PharService
 */
class PharServiceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var PharDownloader|ObjectProphecy
     */
    private $downloader;

    /**
     * @var PharInstaller|ObjectProphecy
     */
    private $installer;

    /**
     * @var PhiveInstallDB|ObjectProphecy
     */
    private $installDB;

    /**
     * @var AliasResolver|ObjectProphecy
     */
    private $resolver;

    /**
     * @var Cli\Output|ObjectProphecy
     */
    private $output;

    /**
     * @var SourceRepositoryLoader|ObjectProphecy
     */
    private $pharIoRepositoryFactory;

    public function testInstallByUrlDownloadsPharAndInvokesInstaller() {
        $url = new Url('https://example.com/foo-1.20.1.phar');
        $release = new Release(new Version('1.20.1'), $url, null);
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $expectedPhar = new Phar('foo', new Version('1.20.1'), $file);

        $this->installDB->hasPhar('foo', new Version('1.20.1'))
            ->shouldBeCalled()
            ->willReturn(false);

        $this->installDB->addPhar($expectedPhar)
            ->shouldBeCalled();

        $this->downloader->download($release)
            ->shouldBeCalled()
            ->willReturn($file);

        $this->installDB->addUsage(
            $expectedPhar,
            '/tmp/foo'
        )->shouldBeCalled();

        $this->installer->install(
            $file,
            '/tmp/foo',
            true
        )->shouldBeCalled();

        $this->getPharService()->install($requestedPhar, '/tmp', true);
    }

    /**
     * @return PharService
     */
    private function getPharService() {
        return new PharService(
            $this->downloader->reveal(),
            $this->installer->reveal(),
            $this->installDB->reveal(),
            $this->resolver->reveal(),
            $this->output->reveal(),
            $this->pharIoRepositoryFactory->reveal()
        );
    }

    public function testInstallByUrlGetsPharFromRepositoryAndInvokesInstaller() {
        $url = new Url('https://example.com/foo-1.20.1.phar');
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $phar = new Phar('foo', new Version('1.20.1'), $file);

        $this->installDB->hasPhar('foo', new Version('1.20.1'))
            ->shouldBeCalled()
            ->willReturn(true);

        $this->installDB->getPhar('foo', new Version('1.20.1'))
            ->shouldBeCalled()
            ->willReturn($phar);

        $this->installDB->addUsage(
            $phar,
            '/tmp/foo'
        )->shouldBeCalled();

        $this->installer->install(
            $file,
            '/tmp/foo',
            true
        )->shouldBeCalled();

        $this->getPharService()->install($requestedPhar, '/tmp', true);
    }

    public function testUpdate()
    {
        $url = new Url('https://example.com/foo-1.20.1.phar');
        $file = new File(new Filename('foo.phar'), 'bar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $phar = new Phar('foo', new Version('1.20.1'), $file);

        $this->installDB->hasPhar('foo', new Version('1.20.1'))
            ->shouldBeCalled()
            ->willReturn(true);

        $this->installDB->getPhar('foo', new Version('1.20.1'))
            ->shouldBeCalled()
            ->willReturn($phar);

        $this->installDB->addUsage(
            $phar,
            '/tmp/foo'
        )->shouldBeCalled();

        $this->installer->install(
            $file,
            '/tmp/foo',
            false
        )->shouldBeCalled();

        $this->getPharService()->update($requestedPhar, '/tmp');
    }

    public function testInstallSkipsPharIfAlreadyInstalled()
    {
        $this->markTestSkipped('This test is dubious');

        $url = new Url('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $this->downloader->download(
            Argument::cetera()
        )->shouldBeCalled()->willReturn(new File(new Filename('phpunit-5.2.10.phar'),''));

        $this->installDB->hasPhar(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->installDB->getPhar(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->output->writeInfo(
            Argument::any()
        )->shouldBeCalled();

        $this->installDB->addUsage(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->installer->install(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->installDB->addPhar(
            Argument::cetera()
        )->shouldBeCalled();

        $this->getPharService()->install($requestedPhar, __DIR__ .'/fixtures/tools', false);
    }

    public function testInstallHandlesDownloadFailedException()
    {
        $url = new Url('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $this->installDB->hasPhar(
            Argument::cetera()
        )->willReturn(false);

        $this->downloader->download(
            Argument::cetera()
        )->willThrow(new DownloadFailedException());

        $this->output->writeError(
            Argument::any()
        )->shouldNotBeCalled();

        $this->installer->install(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->expectException(DownloadFailedException::class);
        $this->getPharService()->install($requestedPhar, '/tmp', false);
    }

    public function testInstallHandlesPharRepositoryException()
    {
        $url = new Url('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $this->installDB->hasPhar(
            Argument::cetera()
        )->willReturn(true);

        $this->installDB->getPhar(
            Argument::cetera()
        )->willThrow(new PharRepositoryException());

        $this->output->writeError(
            Argument::any()
        )->shouldNotBeCalled();

        $this->installer->install(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->expectException(PharRepositoryException::class);
        $this->getPharService()->install($requestedPhar, '/tmp', false);
    }

    public function testInstallHandlesVerificationFailedException()
    {
        $url = new Url('https://example.com/phpunit-5.2.10.phar');
        $requestedPhar = RequestedPhar::fromUrl($url);

        $this->installDB->hasPhar(
            Argument::cetera()
        )->willReturn(false);

        $this->downloader->download(
            Argument::cetera()
        )->willThrow(new VerificationFailedException());

        $this->output->writeError(
            Argument::any()
        )->shouldNotBeCalled();

        $this->installer->install(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->expectException(VerificationFailedException::class);
        $this->getPharService()->install($requestedPhar, '/tmp', false);
    }

    public function testInstallHandlesResolveException()
    {
        $requestedPhar = RequestedPhar::fromAlias(new PharAlias('phpunit', new AnyVersionConstraint()));

        $this->resolver->resolve($requestedPhar->getAlias())
            ->willThrow(new ResolveException());

        $this->output->writeError(
            Argument::any()
        )->shouldNotBeCalled();

        $this->installer->install(
            Argument::cetera()
        )->shouldNotBeCalled();

        $this->expectException(ResolveException::class);
        $this->getPharService()->install($requestedPhar, '/tmp', false);
    }

    protected function setUp() {
        $this->downloader = $this->prophesize(PharDownloader::class);
        $this->installer = $this->prophesize(PharInstaller::class);
        $this->installDB = $this->prophesize(PhiveInstallDB::class);
        $this->resolver = $this->prophesize(AliasResolver::class);
        $this->output = $this->prophesize(Cli\Output::class);
        $this->pharIoRepositoryFactory = $this->prophesize(SourceRepositoryLoader::class);
    }

}



