<?php
namespace PharIo\Phive {

    use TheSeer\CLI\CommandOptions;

    /**
     * @covers PharIo\Phive\PurgeCommandConfig
     */
    class PurgeCommandConfigTest extends \PHPUnit_Framework_TestCase {

        public function testInstantiation() {
            $this->assertInstanceOf(
                PurgeCommandConfig::class,
                new PurgeCommandConfig($this->getOptionsMock(), $this->getConfigMock())
            );
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|CommandOptions
         */
        private function getOptionsMock() {
            return $this->getMockBuilder(CommandOptions::class)
                ->disableOriginalConstructor()->getMock();
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|Config
         */
        private function getConfigMock() {
            return $this->getMockBuilder(Config::class)
                ->disableOriginalConstructor()->getMock();
        }

    }

}

