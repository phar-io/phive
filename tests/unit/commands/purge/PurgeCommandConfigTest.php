<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

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
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Options
     */
    private function getOptionsMock() {
        return $this->getMockBuilder(Cli\Options::class)
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



