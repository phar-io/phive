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

        $config->expects($this->any())
            ->method('getWorkingDirectory')
            ->willReturn('/foo');

        $config->expects($this->once())
            ->method('getRequestedPhars')
            ->will($this->returnValue([$requestedPhar1, $requestedPhar2]));

        $command = new UpdateCommand($config, $pharService, $this->getPhiveXmlConfigMock());

        $pharService->expects($this->at(0))
            ->method('update')
            ->with($requestedPhar1, '/foo');

        $pharService->expects($this->at(1))
            ->method('update')
            ->with($requestedPhar2, '/foo');

        $command->execute();

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UpdateCommandConfig
     */
    private function getCommandConfigMock() {
        return $this->getMockBuilder(UpdateCommandConfig::class)
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

}




