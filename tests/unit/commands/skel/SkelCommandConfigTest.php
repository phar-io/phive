<?php
namespace PharIo\Phive {

    use TheSeer\CLI;
    use Prophecy\Prophecy\ObjectProphecy;

    /**
     * @covers PharIo\Phive\SkelCommandConfig
     */
    class SkelCommandConfigTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var CLI\CommandOptions|ObjectProphecy
         */
        private $cliOptionsProphecy;

        protected function setUp() {
            $this->cliOptionsProphecy = $this->prophesize(CLI\CommandOptions::class);
        }

        /**
         * @dataProvider allowOverwriteProvider
         *
         * @param bool $switch
         */
        public function testAllowOverwrite($switch) {
            $this->cliOptionsProphecy->isSwitch('force')->willReturn($switch);
            $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');

            $this->assertSame($switch, $config->allowOverwrite());
        }

        public function allowOverwriteProvider() {
            return [
                [true],
                [false]
            ];
        }

        public function testGetDestination() {
            $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
            $this->assertEquals('/tmp/phive.xml', $config->getDestination());
        }

        public function testGetTemplateFilename() {
            $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
            $expected = realpath(__DIR__ . '/../../../../conf/phive.skeleton.xml');
            $actual = realpath($config->getTemplateFilename());
            $this->assertEquals($expected, $actual);
        }

    }

}

