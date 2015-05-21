<?php
namespace TheSeer\Phive {

    use Prophecy\Prophecy\MethodProphecy;

    /**
     * @covers TheSeer\Phive\CommandLocator
     */
    class CommandLocatorTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider commandProvider
         */
        public function testValidCommandsAreReturned($command, $factoryMethod, CLICommandOptions $arguments = NULL) {
            $factory = $this->prophesize(Factory::class);

            if ($arguments != NULL) {
                $method = new MethodProphecy($factory, $factoryMethod, [$arguments]);
            } else {
                $method = new MethodProphecy($factory, $factoryMethod, []);
            }
            $method->willReturn($this->prophesize(CommandInterface::class)->reveal());

            $factory->addMethodProphecy($method);
            $locator = new CommandLocator($factory->reveal());

            $request = $this->prophesize(CLIRequest::class);
            $request->getCommand()->willReturn($command)->shouldBeCalled();
            $request->getCommandOptions()->willReturn($arguments);

            $result = $locator->getCommandForRequest($request->reveal());
            $this->assertInstanceOf(CommandInterface::class, $result);
        }

        public function commandProvider() {
            return [
                'help' => ['help', 'getHelpCommand'],
                'version' => ['version', 'getVersionCommand'],
                'install' => ['install', 'getInstallCommand', new CLICommandOptions([])]
            ];
        }

        /**
         * @expectedException \TheSeer\Phive\CommandLocatorException
         * @expectedExceptionCode \TheSeer\Phive\CommandLocatorException::UnknownCommand
         */
        public function testRequestingAnUnknownCommandThrowsException() {
            $factory = $this->prophesize(Factory::class);
            $locator = new CommandLocator($factory->reveal());

            $request = $this->prophesize(CLIRequest::class);
            $request->getCommand()->willReturn('unknown');

            $locator->getCommandForRequest($request->reveal());
        }

    }

}
