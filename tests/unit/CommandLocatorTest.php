<?php
namespace PharIo\Phive {

    use TheSeer\CLI;
    use Prophecy\Prophecy\MethodProphecy;

    /**
     * @covers PharIo\Phive\CommandLocator
     */
    class CommandLocatorTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider commandProvider
         */
        public function testValidCommandsAreReturned($command, $factoryMethod, CLI\CommandOptions $arguments = NULL) {
            $factory = $this->prophesize(Factory::class);

            if ($arguments != NULL) {
                $method = new MethodProphecy($factory, $factoryMethod, [$arguments]);
            } else {
                $method = new MethodProphecy($factory, $factoryMethod, []);
            }
            $method->willReturn($this->prophesize(CLI\Command::class)->reveal());

            $factory->addMethodProphecy($method);
            $locator = new CommandLocator($factory->reveal());

            $request = $this->prophesize(CLI\Request::class);
            $request->getCommand()->willReturn($command)->shouldBeCalled();
            $request->getCommandOptions()->willReturn($arguments);

            $result = $locator->getCommandForRequest($request->reveal());
            $this->assertInstanceOf(CLI\Command::class, $result);
        }

        public function commandProvider() {
            return [
                'help'    => ['help', 'getHelpCommand'],
                'version' => ['version', 'getVersionCommand'],
                'skel'    => ['skel', 'getSkelCommand', new CLI\CommandOptions([])],
                'install' => ['install', 'getInstallCommand', new CLI\CommandOptions([])],
                'purge' => ['purge', 'getPurgeCommand', new CLI\CommandOptions([])],
                'remove' => ['remove', 'getRemoveCommand', new CLI\CommandOptions([])],
                'update-repository-list' => ['update-repository-list', 'getUpdateRepositoryListCommand'],
            ];
        }

        /**
         * @expectedException \TheSeer\CLI\CommandLocatorException
         * @expectedExceptionCode \TheSeer\CLI\CommandLocatorException::UnknownCommand
         */
        public function testRequestingAnUnknownCommandThrowsException() {
            $factory = $this->prophesize(Factory::class);
            $locator = new CommandLocator($factory->reveal());

            $request = $this->prophesize(CLI\Request::class);
            $request->getCommand()->willReturn('unknown');

            $locator->getCommandForRequest($request->reveal());
        }

    }

}
