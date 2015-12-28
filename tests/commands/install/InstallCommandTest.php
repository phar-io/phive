<?php
namespace PharIo\Phive {

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
                ->method('getWorkingDirectory')
                ->willReturn('/foo');

            $config->expects($this->once())
                ->method('getRequestedPhars')
                ->will($this->returnValue([$requestedPhar1, $requestedPhar2]));

            $command = new InstallCommand($config, $pharService);

            $pharService->expects($this->at(0))
                ->method('install')
                ->with($requestedPhar1, '/foo');

            $pharService->expects($this->at(1))
                ->method('install')
                ->with($requestedPhar2, '/foo');

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

    }

}


