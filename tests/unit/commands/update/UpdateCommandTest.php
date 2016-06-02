<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\UpdateCommand
 */
class UpdateCommandTest extends \PHPUnit_Framework_TestCase {

    public function testInvokesPharService() {

        $config = $this->getCommandConfigMock();
        $pharService = $this->getPharServiceMock();

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $installedPhar1 = $this->getPharMock();
        $installedPhar2 = $this->getPharMock();

        $directory = $this->getDirectoryMock();

        $config->expects($this->any())
            ->method('getTargetDirectory')
            ->willReturn($directory);

        $config->expects($this->once())
            ->method('getRequestedPhars')
            ->will($this->returnValue([$requestedPhar1, $requestedPhar2]));

        $command = new UpdateCommand($config, $pharService, $this->getPhiveXmlConfigMock());

        $pharService->expects($this->at(0))
            ->method('update')
            ->with($requestedPhar1, $directory)
            ->willReturn($installedPhar1);

        $pharService->expects($this->at(1))
            ->method('update')
            ->with($requestedPhar2, $directory)
            ->willReturn($installedPhar2);

        $command->execute();

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UpdateCommandConfig
     */
    private function getCommandConfigMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(UpdateCommandConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharService
     */
    private function getPharServiceMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PharService::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(RequestedPhar::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Phar
     */
    private function getPharMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Phar::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PhiveXmlConfig::class);
    }

}




