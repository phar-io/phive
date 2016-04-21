<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\InstallCommand
 */
class InstallCommandTest extends \PHPUnit_Framework_TestCase {

    public function testInvokesPharService() {

        $config = $this->getCommandConfigMock();
        $pharService = $this->getPharServiceMock();

        $requestedPhar1 = $this->getRequestedPharMock();
        $requestedPhar2 = $this->getRequestedPharMock();

        $config->expects($this->any())
            ->method('getTargetDirectory')
            ->willReturn(new Directory(__DIR__));

        $config->expects($this->once())
            ->method('getRequestedPhars')
            ->will($this->returnValue([$requestedPhar1, $requestedPhar2]));

        $command = new InstallCommand($config, $pharService, $this->getPhiveXmlConfigMock(), $this->getEnvironmentMock());

        $pharService->expects($this->at(0))
            ->method('install')
            ->with($requestedPhar1, new Directory(__DIR__));

        $pharService->expects($this->at(1))
            ->method('install')
            ->with($requestedPhar2, new Directory(__DIR__));

        $command->execute();

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|InstallCommandConfig
     */
    private function getCommandConfigMock() {
        return $this->getMockBuilder(InstallCommandConfig::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharService
     */
    private function getPharServiceMock() {
        return $this->getMockBuilder(PharService::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->getMockBuilder(RequestedPhar::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->getMockBuilder(PhiveXmlConfig::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()->getMock();
    }

    public function testAddsEntryToPhiveXml() {
        $config = $this->getCommandConfigMock();
        $pharService = $this->getPharServiceMock();
        $phiveXmlConfig = $this->getPhiveXmlConfigMock();
        $phiveXmlConfig->method('hasTargetDirectory')->willReturn(true);

        $requestedPhar = $this->getRequestedPharMock();

        $config->expects($this->once())
            ->method('getRequestedPhars')
            ->will($this->returnValue([$requestedPhar]));

        $phiveXmlConfig->expects($this->once())
            ->method('addPhar')
            ->with($requestedPhar);

        $command = new InstallCommand($config, $pharService, $phiveXmlConfig, $this->getEnvironmentMock());

        $command->execute();
    }

    public function testDoNotAddEntryToPhiveXml() {
        $config = $this->getCommandConfigMock();
        $pharService = $this->getPharServiceMock();
        $phiveXmlConfig = $this->getPhiveXmlConfigMock();

        $requestedPhar = $this->getRequestedPharMock();

        $config->expects($this->once())
            ->method('getRequestedPhars')
            ->will($this->returnValue([$requestedPhar]));

        $config->expects($this->atLeastOnce())
            ->method('doNotAddToPhiveXml')
            ->willReturn(true);

        $phiveXmlConfig->expects($this->never())
            ->method('addPhar');

        $command = new InstallCommand($config, $pharService, $phiveXmlConfig, $this->getEnvironmentMock());

        $command->execute();
    }

}




