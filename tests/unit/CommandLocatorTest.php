<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;
use Prophecy\Prophecy\MethodProphecy;

/**
 * @covers PharIo\Phive\CommandLocator
 */
class CommandLocatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider commandProvider
     */
    public function testValidCommandsAreReturned($command, $factoryMethod, Cli\Options $arguments = null) {
        $factory = $this->prophesize(Factory::class);

        if ($arguments != null) {
            $method = new MethodProphecy($factory, $factoryMethod, [$arguments]);
        } else {
            $method = new MethodProphecy($factory, $factoryMethod, []);
        }
        $method->willReturn($this->prophesize(Cli\Command::class)->reveal());

        $factory->addMethodProphecy($method);
        $locator = new CommandLocator($factory->reveal());

        $request = $this->prophesize(Cli\Request::class);
        $request->getCommand()->willReturn($command)->shouldBeCalled();
        $request->getCommandOptions()->willReturn($arguments);

        $result = $locator->getCommandForRequest($request->reveal());
        $this->assertInstanceOf(CLI\Command::class, $result);
    }

    public function commandProvider() {
        return [
            'help'                   => ['help', 'getHelpCommand'],
            'version'                => ['version', 'getVersionCommand'],
            'skel'                   => ['skel', 'getSkelCommand', new Cli\Options([])],
            'install'                => ['install', 'getInstallCommand', new Cli\Options([])],
            'purge'                  => ['purge', 'getPurgeCommand', new Cli\Options([])],
            'remove'                 => ['remove', 'getRemoveCommand', new Cli\Options([])],
            'update-repository-list' => ['update-repository-list', 'getUpdateRepositoryListCommand'],
        ];
    }

    /**
     * @expectedException \PharIo\Phive\Cli\CommandLocatorException
     * @expectedExceptionCode \PharIo\Phive\Cli\CommandLocatorException::UnknownCommand
     */
    public function testRequestingAnUnknownCommandThrowsException() {
        $factory = $this->prophesize(Factory::class);
        $locator = new CommandLocator($factory->reveal());

        $request = $this->prophesize(Cli\Request::class);
        $request->getCommand()->willReturn('unknown');

        $locator->getCommandForRequest($request->reveal());
    }

}


