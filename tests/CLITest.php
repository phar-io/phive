<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\CLI
     */

    class CLITest extends \PHPUnit_Framework_TestCase {

        public function testValidCLIRequestGetsExecuted() {
            $request = new CLIRequest([]);
            $command = $this->prophesize(Command::class);

            $locator  =  $this->prophesize(CommandLocator::class);
            $locator->getCommandForRequest($request)->willReturn($command->reveal())->shouldBeCalled();

            $cli = new CLI($locator->reveal());
            $cli->run($request);
        }

        /**
         * @expectedException \PharIo\Phive\CommandLocatorException
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
