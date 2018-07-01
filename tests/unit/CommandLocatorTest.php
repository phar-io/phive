<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\MethodProphecy;

/**
 * @covers \PharIo\Phive\CommandLocator
 */
class CommandLocatorTest extends TestCase {

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

        $result = $locator->getCommand($command);
        $this->assertInstanceOf(CLI\Command::class, $result);
    }

    public function commandProvider() {
        return [
            ''                       => ['', 'getDefaultCommand'],
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
            'composer'               => ['composer', 'getComposerCommand'],
            'status'                 => ['status', 'getStatusCommand'],
            'selfupdate'             => ['selfupdate', 'getSelfupdateCommand'],
            'self-update'            => ['self-update', 'getSelfupdateCommand'],
        ];
    }

    public function testRequestingAnUnknownCommandThrowsException() {
        $factory = $this->prophesize(Factory::class);
        $locator = new CommandLocator($factory->reveal());

        $this->expectException(\PharIo\Phive\Cli\CommandLocatorException::class);
        $this->expectExceptionCode(\PharIo\Phive\Cli\CommandLocatorException::UnknownCommand);

        $locator->getCommand('unknown');
    }

}
