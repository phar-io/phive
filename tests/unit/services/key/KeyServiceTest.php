<?php
namespace PharIo\Phive {

    use Prophecy\Argument;
    use Prophecy\Prophecy\ObjectProphecy;

    /**
     * @covers PharIo\Phive\KeyService
     */
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

        /**
         * @var Input|ObjectProphecy
         */
        private $input;

        public function setUp() {
            $this->downloader = $this->prophesize(KeyDownloader::class);
            $this->importer = $this->prophesize(KeyImporter::class);
            $this->output = $this->prophesize(Output::class);
            $this->input = $this->prophesize(Input::class);
        }

        public function testInvokesKeyDownloader() {
            $this->downloader->download('foo')->willReturn('some key');

            $this->assertEquals('some key', $this->getKeyService()->downloadKey('foo'));
        }

        public function testInvokesImporter() {
            $this->input->confirm(Argument::any())
                ->willReturn(true);

            $this->importer->importKey('some key')->willReturn(['keydata']);

            $this->assertEquals(['keydata'], $this->getKeyService()->importKey('some id', 'some key'));
        }

        /**
         * @expectedException \PharIo\Phive\VerificationFailedException
         */
        public function testImportKeyWillThrowExceptionIfUserDeclinedImport() {
            $this->input->confirm(Argument::any())
                ->willReturn(false);

            $this->getKeyService()->importKey('some id', 'some key');
        }

        /**
         * @return KeyService
         */
        private function getKeyService() {
            return new KeyService(
                $this->downloader->reveal(), $this->importer->reveal(), $this->output->reveal(), $this->input->reveal()
            );
        }
    }

}