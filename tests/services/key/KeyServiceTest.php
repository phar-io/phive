<?php
namespace TheSeer\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

    class KeyServiceTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var KeyDownloaderInterface|ObjectProphecy
         */
        private $downloader;

        /**
         * @var KeyImporterInterface|ObjectProphecy
         */
        private $importer;

        /**
         * @var LoggerInterface|ObjectProphecy
         */
        private $logger;

        public function setUp() {
            $this->downloader = $this->prophesize(KeyDownloaderInterface::class);
            $this->importer = $this->prophesize(KeyImporterInterface::class);
            $this->logger = $this->prophesize(LoggerInterface::class);
        }

        public function testInvokesKeyDownloader() {
            $this->downloader->download('foo')->willReturn('some key');

            $service = new KeyService(
                $this->downloader->reveal(), $this->importer->reveal(), $this->logger->reveal()
            );

            $this->assertEquals('some key', $service->downloadKey('foo'));
        }

        public function testInvokesImporter() {
            $this->importer->importKey('some key')->willReturn(['keydata']);

            $service = new KeyService(
                $this->downloader->reveal(), $this->importer->reveal(), $this->logger->reveal()
            );

            $this->assertEquals(['keydata'], $service->importKey('some key'));
        }

    }

}

