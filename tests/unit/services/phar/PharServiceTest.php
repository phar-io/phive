<?php
namespace PharIo\Phive {

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
         * @var PharRepository|ObjectProphecy
         */
        private $repository;

        /**
         * @var AliasResolver|ObjectProphecy
         */
        private $resolver;

        /**
         * @var Output|ObjectProphecy
         */
        private $output;

        /**
         * @var PharIoRepositoryFactory|ObjectProphecy
         */
        private $pharIoRepositoryFactory;

        public function testInstallByUrlDownloadsPharAndInvokesInstaller() {
            $url = new Url('https://example.com/foo-1.20.1.phar');
            $release = new Release(new Version('1.20.1'), $url, null);
            $file = new File(new Filename('foo.phar'), 'bar');
            $requestedPhar = RequestedPhar::fromUrl($url);

            $expectedPhar = new Phar('foo', new Version('1.20.1'), $file);

            $this->repository->hasPhar('foo', new Version('1.20.1'))
                ->shouldBeCalled()
                ->willReturn(false);

            $this->repository->addPhar($expectedPhar)
                ->shouldBeCalled();

            $this->downloader->download($release)
                ->shouldBeCalled()
                ->willReturn($file);

            $this->repository->addUsage(
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
                $this->repository->reveal(),
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

            $this->repository->hasPhar('foo', new Version('1.20.1'))
                ->shouldBeCalled()
                ->willReturn(true);

            $this->repository->getPhar('foo', new Version('1.20.1'))
                ->shouldBeCalled()
                ->willReturn($phar);

            $this->repository->addUsage(
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

        /**
         * @dataProvider invalidUrlProvider
         * @expectedException \PharIo\Phive\DownloadFailedException
         *
         * @param $urlString
         */
        public function testInstallByUrlThrowsExceptionIfUrlDoesNotContainValidPharName($urlString) {
            $url = new Url($urlString);
            $requestedPhar = RequestedPhar::fromUrl($url);
            $this->getPharService()->install($requestedPhar, '/tmp', true);
        }

        public function invalidUrlProvider() {
            return [
                ['https://example.com/foo.phar'],
                ['https://example.com/bar120.phar'],
                ['https://example.com/foo-1.2.phar'],
                ['https://example.com/foo1.2.0.phar'],
            ];
        }

        protected function setUp() {
            $this->downloader = $this->prophesize(PharDownloader::class);
            $this->installer = $this->prophesize(PharInstaller::class);
            $this->repository = $this->prophesize(PharRepository::class);
            $this->resolver = $this->prophesize(AliasResolver::class);
            $this->output = $this->prophesize(Output::class);
            $this->pharIoRepositoryFactory = $this->prophesize(PharIoRepositoryFactory::class);
        }

    }

}

