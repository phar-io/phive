<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Input;

/**
 * @covers PharIo\Phive\ComposerCommand
 */
class ComposerCommandTest extends \PHPUnit_Framework_TestCase {

    public function testLetsPharServiceInstallExpectedPhars() {

        $workingDirectory = $this->getDirectoryMock();

        $config = $this->getComposerCommandConfigMock();
        $config->method('getTargetDirectory')->willReturn($workingDirectory);
        $config->method('makeCopy')->willReturn(false);
        $config->method('getComposerFilename')->willReturn(new Filename('foo'));

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $composerService = $this->getComposerServiceMock();
        $composerService->method('findCandidates')
            ->willReturn([$requestedPhar1, $requestedPhar2]);

        $pharService = $this->getPharServiceMock();
        $pharService->expects($this->at(0))
            ->method('install')
            ->with(
                $this->identicalTo($requestedPhar1),
                $workingDirectory,
                false
            )->willReturn($this->getInstalledPharMock());
        $pharService->expects($this->at(1))
            ->method('install')
            ->with(
                $this->identicalTo($requestedPhar2),
                $workingDirectory,
                false
            )->willReturn($this->getInstalledPharMock());

        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $command = new ComposerCommand(
            $config,
            $composerService,
            $pharService,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $input
        );

        $command->execute();
    }

    public function testLetsPharServiceInstallExpectedPharsGlobally() {

        $config = $this->getComposerCommandConfigMock();
        $config->method('installGlobally')->willReturn(true);
        $config->method('makeCopy')->willReturn(false);
        $config->method('getComposerFilename')->willReturn(new Filename('foo'));

        $environment = $this->getEnvironmentMock();
        $environment->method('getBinaryName')
            ->willReturn(__DIR__ . '/php');

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $composerService = $this->getComposerServiceMock();
        $composerService->method('findCandidates')
            ->willReturn([$requestedPhar1, $requestedPhar2]);

        $pharService = $this->getPharServiceMock();
        $pharService->expects($this->at(0))
            ->method('install')
            ->with(
                $this->identicalTo($requestedPhar1),
                __DIR__,
                false
            );
        $pharService->expects($this->at(1))
            ->method('install')
            ->with(
                $this->identicalTo($requestedPhar2),
                __DIR__,
                false
            );

        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $command = new ComposerCommand(
            $config,
            $composerService,
            $pharService,
            $this->getPhiveXmlConfigMock(),
            $environment,
            $input
        );

        $command->execute();
    }

    public function testDoesNotInstallPharsIfUserDidNotConfirm() {

        $workingDirectory = $this->getDirectoryMock();

        $config = $this->getComposerCommandConfigMock();
        $config->method('getTargetDirectory')->willReturn($workingDirectory);
        $config->method('makeCopy')->willReturn(false);
        $config->method('getComposerFilename')->willReturn(new Filename('foo'));

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $composerService = $this->getComposerServiceMock();
        $composerService->method('findCandidates')
            ->willReturn([$requestedPhar1, $requestedPhar2]);

        $pharService = $this->getPharServiceMock();
        $pharService->expects($this->never())
            ->method('install');

        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(false);

        $command = new ComposerCommand(
            $config,
            $composerService,
            $pharService,
            $this->getPhiveXmlConfigMock(),
            $this->getEnvironmentMock(),
            $input
        );

        $command->execute();
    }

    public function testAddsExpectedPharsToPhiveXmlConfig() {

        $workingDirectory = $this->getDirectoryMock();

        $config = $this->getComposerCommandConfigMock();
        $config->method('getTargetDirectory')->willReturn($workingDirectory);
        $config->method('makeCopy')->willReturn(false);
        $config->method('getComposerFilename')->willReturn(new Filename('foo'));
        $config->method('doNotAddToPhiveXml')->willReturn(false);

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $installedPhar1 = $this->getInstalledPharMock();
        $installedPhar2 = $this->getInstalledPharMock();

        $pharService = $this->getPharServiceMock();

        $pharService->expects($this->at(0))
            ->method('install')
            ->with($requestedPhar1, $workingDirectory)
            ->willReturn($installedPhar1);

        $pharService->expects($this->at(1))
            ->method('install')
            ->with($requestedPhar2, $workingDirectory)
            ->willReturn($installedPhar2);

        $composerService = $this->getComposerServiceMock();
        $composerService->method('findCandidates')
            ->willReturn([$requestedPhar1, $requestedPhar2]);

        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->at(0))
            ->method('addPhar')
            ->with($this->identicalTo($installedPhar1));

        $phiveXmlConfig->expects($this->at(1))
            ->method('addPhar')
            ->with($this->identicalTo($installedPhar2));

        $command = new ComposerCommand(
            $config,
            $composerService,
            $pharService,
            $phiveXmlConfig,
            $this->getEnvironmentMock(),
            $input
        );

        $command->execute();
    }

    public function testDoesNotAddPharsToPhiveXmlConfig() {

        $workingDirectory = $this->getDirectoryMock();

        $config = $this->getComposerCommandConfigMock();
        $config->method('getTargetDirectory')->willReturn($workingDirectory);
        $config->method('makeCopy')->willReturn(false);
        $config->method('getComposerFilename')->willReturn(new Filename('foo'));
        $config->method('doNotAddToPhiveXml')->willReturn(true);

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $composerService = $this->getComposerServiceMock();
        $composerService->method('findCandidates')
            ->willReturn([$requestedPhar1, $requestedPhar2]);

        $input = $this->getInputMock();
        $input->method('confirm')->willReturn(true);

        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->expects($this->never())
            ->method('addPhar');

        $command = new ComposerCommand(
            $config,
            $composerService,
            $this->getPharServiceMock(),
            $phiveXmlConfig,
            $this->getEnvironmentMock(),
            $input
        );

        $command->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ComposerCommandConfig
     */
    private function getComposerCommandConfigMock() {
        return $this->createMock(ComposerCommandConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ComposerService
     */
    private function getComposerServiceMock() {
        return $this->createMock(ComposerService::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharService
     */
    private function getPharServiceMock() {
        return $this->createMock(PharService::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Input
     */
    private function getInputMock() {
        return $this->createMock(Input::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->createMock(PhiveXmlConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|InstalledPhar
     */
    private function getInstalledPharMock() {
        return $this->createMock(InstalledPhar::class);
    }
}
