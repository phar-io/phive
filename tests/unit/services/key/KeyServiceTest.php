<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

/**
 * @covers PharIo\Phive\KeyService
 */
class KeyServiceTest extends \PHPUnit_Framework_TestCase {

    public function testInvokesImporter() {
        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $importer = $this->getKeyImporterMock();
        $importer->method('importKey')->willReturn(['keydata']);

        $key = $this->getPublicKeyMock();
        $key->method('getInfo')->willReturn('keyinfo');
        $key->method('getKeyData')->willReturn('some key');

        $downloader = $this->getKeyDownloaderMock();
        $downloader->method('download')->willReturn($key);

        $trusted = $this->createMock(KeyIdCollection::class);

        $service = new KeyService($downloader, $importer, $trusted, $this->getOutputMock(), $input);

        $this->assertEquals(['keydata'], $service->importKey('foo', []));
    }

    public function testOutputsAWarningIfTheKeyChanged() {
        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $importer = $this->getKeyImporterMock();
        $importer->method('importKey')->willReturn(['keydata']);

        $key = $this->getPublicKeyMock();
        $key->method('getInfo')->willReturn('keyinfo');
        $key->method('getKeyData')->willReturn('some key');

        $downloader = $this->getKeyDownloaderMock();
        $downloader->method('download')->willReturn($key);

        $output = $this->getOutputMock();
        $output->expects($this->once())->method('writeWarning');

        $trusted = $this->createMock(KeyIdCollection::class);

        $service = new KeyService($downloader, $importer, $trusted, $output, $input);

        $service->importKey('foo', ['bar']);
    }

    public function testImportKeyWillNotSucceedIfUserDeclinedImport() {
        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(false);

        $key = $this->getPublicKeyMock();
        $key->method('getInfo')->willReturn('keyinfo');
        $key->method('getKeyData')->willReturn('some key');

        $downloader = $this->getKeyDownloaderMock();
        $downloader->method('download')->willReturn($key);

        $trusted = $this->createMock(KeyIdCollection::class);

        $service = new KeyService($downloader, $this->getKeyImporterMock(), $trusted, $this->getOutputMock(), $input);
        $result = $service->importKey('some id', []);
        $this->assertFalse($result->isSuccess());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Input
     */
    private function getInputMock() {
        return $this->createMock(Cli\Input::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Output
     */
    private function getOutputMock() {
        return $this->createMock(Cli\Output::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|KeyDownloader
     */
    private function getKeyDownloaderMock() {
        return $this->createMock(KeyDownloader::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|KeyImporter
     */
    private function getKeyImporterMock() {
        return $this->createMock(KeyImporter::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PublicKey
     */
    private function getPublicKeyMock() {
        return $this->createMock(PublicKey::class);
    }
}

