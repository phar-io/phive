<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\PurgeCommand
     */
    class PurgeCommandTest extends \PHPUnit_Framework_TestCase {

        public function testInvokesRepository() {
            $config = $this->getCommandConfigMock();
            $repository = $this->getPharRepositoryMock();

            $phar1 = $this->getPharMock();
            $phar2 = $this->getPharMock();

            $repository->expects($this->once())
                ->method('getUnusedPhars')
                ->willReturn([$phar1, $phar2]);

            $repository->expects($this->at(1))
                ->method('removePhar')
                ->with($phar1);

            $repository->expects($this->at(2))
                ->method('removePhar')
                ->with($phar2);

            $command = new PurgeCommand($config, $repository, $this->getOutputMock());
            $command->execute();
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|PurgeCommandConfig
         */
        private function getCommandConfigMock() {
            return $this->getMockBuilder(PurgeCommandConfig::class)
                ->disableOriginalConstructor()->getMock();
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|PharRepository
         */
        private function getPharRepositoryMock() {
            return $this->getMockBuilder(PharRepository::class)
                ->disableOriginalConstructor()->getMock();
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|Phar
         */
        private function getPharMock() {
            $version = $this->getMockBuilder(Version::class)->disableOriginalConstructor()->getMock();
            $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
            return new Phar('foo', $version, $file);
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|Output
         */
        private function getOutputMock() {
            return $this->getMockBuilder(Output::class)
                ->disableOriginalConstructor()->getMock();
        }

        public function testWritesToOutput() {
            $config = $this->getCommandConfigMock();
            $repository = $this->getPharRepositoryMock();
            $output = $this->getOutputMock();

            $phar1 = $this->getPharMock();

            $repository->expects($this->once())
                ->method('getUnusedPhars')
                ->willReturn([$phar1]);

            $output->expects($this->once())
                ->method('writeInfo');

            $command = new PurgeCommand($config, $repository, $output);
            $command->execute();
        }
    }

}

