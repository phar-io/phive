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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\KeyService
 */
class KeyServiceTest extends TestCase {
    public function testInvokesImporter(): void {
        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $resultMock = $this->createMock(KeyImportResult::class);
        $importer   = $this->getKeyImporterMock();
        $importer->method('importKey')->willReturn($resultMock);

        $key = $this->getPublicKeyMock();
        $key->method('getInfo')->willReturn('keyinfo');
        $key->method('getKeyData')->willReturn('some key');

        $downloader = $this->getKeyDownloaderMock();
        $downloader->method('download')->willReturn($key);

        /** @var MockObject|TrustedCollection $trusted */
        $trusted = $this->createMock(TrustedCollection::class);

        $service = new KeyService($downloader, $importer, $trusted, $this->getOutputMock(), $input);

        $this->assertSame($resultMock, $service->importKey('foo', []));
    }

    public function testOutputsAWarningIfTheKeyChanged(): void {
        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $importer = $this->getKeyImporterMock();
        $importer->method('importKey')->willReturn($this->createMock(KeyImportResult::class));

        $key = $this->getPublicKeyMock();
        $key->method('getInfo')->willReturn('keyinfo');
        $key->method('getKeyData')->willReturn('some key');

        $downloader = $this->getKeyDownloaderMock();
        $downloader->method('download')->willReturn($key);

        $output = $this->getOutputMock();
        $output->expects($this->once())->method('writeWarning');

        $trusted = $this->createMock(TrustedCollection::class);

        $service = new KeyService($downloader, $importer, $trusted, $output, $input);

        $service->importKey('foo', ['bar']);
    }

    public function testImportKeyWillNotSucceedIfUserDeclinedImport(): void {
        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(false);

        $key = $this->getPublicKeyMock();
        $key->method('getInfo')->willReturn('keyinfo');
        $key->method('getKeyData')->willReturn('some key');

        $downloader = $this->getKeyDownloaderMock();
        $downloader->method('download')->willReturn($key);

        $trusted = $this->createMock(TrustedCollection::class);

        $service = new KeyService($downloader, $this->getKeyImporterMock(), $trusted, $this->getOutputMock(), $input);
        $result  = $service->importKey('some id', []);
        $this->assertFalse($result->isSuccess());
    }

    /**
     * @return Cli\Input|PHPUnit_Framework_MockObject_MockObject
     */
    private function getInputMock() {
        return $this->createMock(Cli\Input::class);
    }

    /**
     * @return Cli\Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputMock() {
        return $this->createMock(Cli\Output::class);
    }

    /**
     * @return KeyDownloader|PHPUnit_Framework_MockObject_MockObject
     */
    private function getKeyDownloaderMock() {
        return $this->createMock(KeyDownloader::class);
    }

    /**
     * @return KeyImporter|PHPUnit_Framework_MockObject_MockObject
     */
    private function getKeyImporterMock() {
        return $this->createMock(KeyImporter::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|PublicKey
     */
    private function getPublicKeyMock() {
        return $this->createMock(PublicKey::class);
    }
}
