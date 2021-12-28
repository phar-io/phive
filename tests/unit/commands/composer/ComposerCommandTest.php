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

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Input;
use PharIo\Version\ExactVersionConstraint;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\ComposerCommand
 */
class ComposerCommandTest extends TestCase {
    /** @var ComposerCommandConfig|PHPUnit_Framework_MockObject_MockObject */
    private $commandConfig;

    /** @var ComposerService|PHPUnit_Framework_MockObject_MockObject */
    private $composerService;

    /** @var InstallService|PHPUnit_Framework_MockObject_MockObject */
    private $installService;

    /** @var Input|PHPUnit_Framework_MockObject_MockObject */
    private $input;

    /** @var PHPUnit_Framework_MockObject_MockObject|RequestedPharResolverService */
    private $pharResolverService;

    /** @var ComposerCommand */
    private $command;

    /** @var PHPUnit_Framework_MockObject_MockObject|ReleaseSelector */
    private $releaseSelector;

    protected function setUp(): void {
        $this->commandConfig       = $this->getComposerCommandConfigMock();
        $this->composerService     = $this->getComposerServiceMock();
        $this->installService      = $this->getInstallServiceMock();
        $this->input               = $this->getInputMock();
        $this->pharResolverService = $this->getRequestedPharResolverServiceMock();
        $this->releaseSelector     = $this->getReleaseSelectorMock();

        $this->command = new ComposerCommand(
            $this->commandConfig,
            $this->composerService,
            $this->installService,
            $this->input,
            $this->pharResolverService,
            $this->releaseSelector
        );
    }

    public function testDoesNotInstallCandidateIfInstallationWasNotConfirmend(): void {
        $this->commandConfig->method('getComposerFilename')
            ->willReturn(new Filename('composer.json'));

        $requestedPhar = $this->getRequestedPharMock();
        $this->composerService->method('findCandidates')->willReturn([$requestedPhar]);
        $this->input->method('confirm')->willReturn(false);

        $this->installService->expects($this->never())->method('execute');

        $this->command->execute();
    }

    public function testDoesInstallsCandidateIfInstallationWasConfirmend(): void {
        $this->commandConfig->method('getTargetDirectory')
            ->willReturn($this->getDirectoryMock());

        $this->commandConfig->method('getComposerFilename')
            ->willReturn(new Filename('composer.json'));

        $requestedPhar = $this->getRequestedPharMock();
        $requestedPhar->method('getLockedVersion')->willReturn(new ExactVersionConstraint('1.0.0'));
        $requestedPhar->method('getVersionConstraint')->willReturn(new ExactVersionConstraint('1.0.0'));
        $requestedPhar->method('hasLocation')->willReturn(true);
        $requestedPhar->method('getLocation')->willReturn(new Filename('destination/foo.phar'));
        $requestedPhar->method('getIdentifier')->willReturn(new PharAlias('Foo'));

        $this->composerService->method('findCandidates')->willReturn([$requestedPhar]);
        $this->input->method('confirm')->willReturn(true);

        $release = $this->getReleaseMock();
        $release->method('getUrl')->willReturn(new PharUrl('https://example.com/foo.phar'));

        $releases = $this->getReleaseCollectionMock();
        //$releases->method('getLatest')->willReturn($release);

        $repository = $this->getSourceRepositoryMock();
        $repository->method('getReleasesByRequestedPhar')
            ->with($requestedPhar)
            ->willReturn($releases);

        $this->pharResolverService->method('resolve')
            ->with($requestedPhar)
            ->willReturn($repository);

        $this->releaseSelector->method('select')
            ->willReturn($release);

        $this->installService->expects($this->once())->method('execute')
            ->with($release);

        $this->command->execute();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|SupportedRelease
     */
    private function getReleaseMock() {
        return $this->createMock(SupportedRelease::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ReleaseCollection
     */
    private function getReleaseCollectionMock() {
        return $this->createMock(ReleaseCollection::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|SourceRepository
     */
    private function getSourceRepositoryMock() {
        return $this->createMock(SourceRepository::class);
    }

    /**
     * @return Directory|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|RequestedPharResolverService
     */
    private function getRequestedPharResolverServiceMock() {
        return $this->createMock(RequestedPharResolverService::class);
    }

    /**
     * @return Input|PHPUnit_Framework_MockObject_MockObject
     */
    private function getInputMock() {
        return $this->createMock(Input::class);
    }

    /**
     * @return ComposerService|PHPUnit_Framework_MockObject_MockObject
     */
    private function getComposerServiceMock() {
        return $this->createMock(ComposerService::class);
    }

    /**
     * @return ComposerCommandConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private function getComposerCommandConfigMock() {
        return $this->createMock(ComposerCommandConfig::class);
    }

    /**
     * @return InstallService|PHPUnit_Framework_MockObject_MockObject
     */
    private function getInstallServiceMock() {
        return $this->createMock(InstallService::class);
    }

    private function getReleaseSelectorMock() {
        return $this->createMock(ReleaseSelector::class);
    }
}
