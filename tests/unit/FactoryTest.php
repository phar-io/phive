<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Request;

/**
 * @covers PharIo\Phive\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider factoryMethodProvider
     *
     * @param string $method
     * @param string $expectedClass
     */
    public function testInstantiation($method, $expectedClass) {
        $request = $this->getRequestMock();
        $options = $this->getOptionsMock();
        $request->method('getCommandOptions')->willReturn($options);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Factory $factory */
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$request])
            ->setMethods(['getSourcesList'])
            ->getMock();
        $factory->method('getSourcesList')->willReturn($this->getMockWithoutInvokingTheOriginalConstructor(SourcesList::class));
        $this->assertInstanceOf($expectedClass, call_user_func([$factory, $method]));
    }

    public function factoryMethodProvider() {
        return [
            ['getRunner', Cli\Runner::class],
            ['getVersionCommand', VersionCommand::class],
            ['getHelpCommand', HelpCommand::class],
            ['getSkelCommand', SkelCommand::class],
            ['getUpdateRepositoryListCommand', UpdateRepositoryListCommand::class],
            ['getRemoveCommand', RemoveCommand::class],
            ['getResetCommand', ResetCommand::class],
            ['getInstallCommand', InstallCommand::class],
            ['getUpdateCommand', UpdateCommand::class],
            ['getListCommand', ListCommand::class],
            ['getPurgeCommand', PurgeCommand::class],
            ['getComposerCommand', ComposerCommand::class]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function getRequestMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Request::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Options
     */
    private function getOptionsMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Cli\Options::class);
    }

}