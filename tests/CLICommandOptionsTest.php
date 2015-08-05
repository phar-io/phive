<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\CLICommandOptions
     */
    class CLICommandOptionsTest extends \PHPUnit_Framework_TestCase {

        /**
         * @expectedException \PharIo\Phive\CLICommandOptionsException
         * @expectedExceptionCode \PharIo\Phive\CLICommandOptionsException::NoSuchOption
         */
        public function testRequestingNonExistingOptionThrowsException() {
            $instance = new CLICommandOptions([]);
            $instance->getOption('foo');
        }

        /**
         * @expectedException \PharIo\Phive\CLICommandOptionsException
         * @expectedExceptionCode \PharIo\Phive\CLICommandOptionsException::InvalidArgumentIndex
         */
        public function testRequestingNonExistingArgumentThrowsException() {
            $instance = new CLICommandOptions([]);
            $instance->getArgument(100);
        }

        public function testOptionsGetParsedCorrectlyWhenValueIsAssignedWithEqualSign() {
            $instance = new CLICommandOptions(['--foo=abc']);
            $this->assertEquals('abc', $instance->getOption('foo'));
        }

        public function testOptionsGetParsedCorrectlyWhenValueIsAssignedWithSpace() {
            $instance = new CLICommandOptions(['--foo', 'abc', 'more']);
            $this->assertEquals('abc', $instance->getOption('foo'));
        }

        public function testSwitchesGetParsedFromRequest() {
            $instance = new CLICommandOptions(['--bar=abc','-f','bar']);
            $this->assertTrue($instance->isSwitch('f'));
            $this->assertFalse($instance->isSwitch('x'));
        }

        public function testArgumentsAreParsedCorrectlyFromRequest() {
            $instance = new CLICommandOptions(['--foo', 'abc', '-f', 'arg1','arg2']);
            $this->assertEquals(2, $instance->getArgumentCount());
            $this->assertEquals('arg1', $instance->getArgument(0));
            $this->assertEquals('arg2', $instance->getArgument(1));
        }

    }

}
