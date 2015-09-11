<?php
namespace PharIo\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

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

        protected function setUp() {
            $this->downloader = $this->prophesize(PharDownloader::class);
            $this->installer = $this->prophesize(PharInstaller::class);
            $this->repository = $this->prophesize(PharRepository::class);
        }

        public function testInstallInvokesInstallerAsExpected() {
            $file = new File('foo.phar', 'bar');
            $phar = new Phar('foo', new Version('1.20.1'), $file);
            $this->installer->install(
                $file,
                '/tmp/foo',
                false
            )->shouldBeCalled();
            $this->getPharService()->install($phar, '/tmp', false);
        }

        public function testInstallInvokesRepositoryAsExpected() {
            $file = new File('foo.phar', 'bar');
            $phar = new Phar('foo', new Version('1.20.1'), $file);
            $this->repository->addUsage(
                $phar,
                '/tmp/foo'
            )->shouldBeCalled();
            $this->getPharService()->install($phar, '/tmp', false);
        }

        public function testInstallByUrlDownloadsPharAndInvokesInstaller() {
            $url = new Url('https://example.com/foo-1.20.1.phar');
            $file = new File('foo.phar', 'bar');
            $expectedPhar = new Phar('foo', new Version('1.20.1'), $file);

            $this->repository->hasPhar('foo', new Version('1.20.1'))
                ->shouldBeCalled()
                ->willReturn(false);

            $this->downloader->download($url)
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

            $this->getPharService()->installByUrl($url, '/tmp', true);
        }

        public function testInstallByUrlGetsPharFromRepositoryAndInvokesInstaller() {
            $url = new Url('https://example.com/foo-1.20.1.phar');
            $file = new File('foo.phar', 'bar');
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

            $this->getPharService()->installByUrl($url, '/tmp', true);
        }

        /**
         * @dataProvider invalidUrlProvider
         * @expectedException \PharIo\Phive\DownloadFailedException
         */
        public function testInstallByUrlThrowsExceptionIfUrlDoesNotContainValidPharName($urlString) {
            $url = new Url($urlString);
            $this->getPharService()->installByUrl($url, '/tmp', true);
        }

        public function invalidUrlProvider() {
            return [
                ['https://example.com/foo.phar'],
                ['https://example.com/bar120.phar'],
                ['https://example.com/foo-1.2.phar'],
                ['https://example.com/foo1.2.0.phar'],
            ];
        }

        /**
         * @return PharService
         */
        private function getPharService() {
            return new PharService($this->downloader->reveal(), $this->installer->reveal(), $this->repository->reveal());
        }

    }

}

