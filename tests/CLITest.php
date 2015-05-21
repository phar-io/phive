<?php
namespace TheSeer\Phive {

    /**
     * @covers TheSeer\Phive\CLI
     */

    class CLITest extends \PHPUnit_Framework_TestCase {

        public function testValidCLIRequestGetsExecuted() {
            $request = new CLIRequest([]);
            $command = $this->prophesize(CommandInterface::class);

            $locator  =  $this->prophesize(CommandLocator::class);
            $locator->getCommandForRequest($request)->willReturn($command->reveal())->shouldBeCalled();

            $cli = new CLI($locator->reveal());
            $cli->run($request);
        }

        /**
         * @expectedException \TheSeer\Phive\CommandLocatorException
         */
        public function testGeneralExceptionsArePassedOn() {
            $request = new CLIRequest([]);
            $locator  =  $this->prophesize(CommandLocator::class);
            $locator->getCommandForRequest($request)->willThrow(new CommandLocatorException());
            $cli = new CLI($locator->reveal());
            $cli->run($request);
        }
    }

}
