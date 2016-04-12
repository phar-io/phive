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
     *
     * @param $command
     * @param $factoryMethod
     *
     * @throws Cli\CommandLocatorException
     */
    public function testValidCommandsAreReturned($command, $factoryMethod) {
        $factory = $this->prophesize(Factory::class);

        $method = new MethodProphecy($factory, $factoryMethod, []);
        $method->willReturn($this->prophesize(Cli\Command::class)->reveal());

        $factory->addMethodProphecy($method);
        $locator = new CommandLocator($factory->reveal());

        $request = $this->prophesize(Cli\Request::class);
        $request->getCommand()->willReturn($command)->shouldBeCalled();

        $result = $locator->getCommandForRequest($request->reveal());
        $this->assertInstanceOf(CLI\Command::class, $result);
    }

    public function commandProvider() {
        return [
            'help'                   => ['help', 'getHelpCommand'],
            'version'                => ['version', 'getVersionCommand'],
            'skel'                   => ['skel', 'getSkelCommand'],
            'install'                => ['install', 'getInstallCommand'],
            'list'                   => ['list', 'getListCommand'],
            'purge'                  => ['purge', 'getPurgeCommand'],
            'remove'                 => ['remove', 'getRemoveCommand'],
            'reset'                  => ['reset', 'getResetCommand'],
            'update'                 => ['update', 'getUpdateCommand'],
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


