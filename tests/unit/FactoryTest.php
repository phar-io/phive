<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider factoryMethodProvider
     *
     * @param string $method
     * @param array $parameters
     * @param string $expectedClass
     */
    public function testInstantiation($method, array $parameters, $expectedClass) {
        $factory = new Factory();
        $this->assertInstanceOf($expectedClass, call_user_func_array([$factory, $method], $parameters));
    }

    public function factoryMethodProvider() {
        return [
            ['getRunner', [], Cli\Runner::class],
            ['getVersionCommand', [], VersionCommand::class],
            ['getHelpCommand', [], HelpCommand::class],
            ['getSkelCommand', [$this->getOptionsMock()], SkelCommand::class],
            ['getUpdateRepositoryListCommand', [], UpdateRepositoryListCommand::class],
            ['getRemoveCommand', [$this->getOptionsMock()], RemoveCommand::class],
            ['getPharService', [], PharService::class],
            ['getSignatureService', [], SignatureService::class],
            ['getResetCommand', [$this->getOptionsMock()], ResetCommand::class],
            ['getKeyService', [], KeyService::class],
            ['getInstallCommand', [$this->getOptionsMock()], InstallCommand::class],
            ['getUpdateCommand', [$this->getOptionsMock()], UpdateCommand::class],
            ['getListCommand', [$this->getOptionsMock()], ListCommand::class],
            ['getPurgeCommand', [$this->getOptionsMock()], PurgeCommand::class]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Options
     */
    private function getOptionsMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Cli\Options::class);
    }

}