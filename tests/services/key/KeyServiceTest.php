<?php
namespace PharIo\Phive {

    use Prophecy\Prophecy\ObjectProphecy;

    class KeyServiceTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var KeyDownloader|ObjectProphecy
         */
        private $downloader;

        /**
         * @var KeyImporter|ObjectProphecy
         */
        private $importer;

        /**
         * @var Output|ObjectProphecy
         */
        private $output;

        public function setUp() {
            $this->downloader = $this->prophesize(KeyDownloader::class);
            $this->importer = $this->prophesize(KeyImporter::class);
            $this->output = $this->prophesize(Output::class);
        }

        public function testInvokesKeyDownloader() {
            $this->downloader->download('foo')->willReturn('some key');

            $service = new KeyService(
                $this->downloader->reveal(), $this->importer->reveal(), $this->output->reveal()
            );

            $this->assertEquals('some key', $service->downloadKey('foo'));
        }

        public function testInvokesImporter() {
            $this->importer->importKey('some key')->willReturn(['keydata']);

            $service = new KeyService(
                $this->downloader->reveal(), $this->importer->reveal(), $this->output->reveal()
            );

            $this->assertEquals(['keydata'], $service->importKey('some key'));
        }

    }

}

