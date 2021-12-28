<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function call_user_func;
use PharIo\Phive\Cli\Request;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\Factory
 */
class FactoryTest extends TestCase {
    /**
     * @dataProvider factoryMethodProvider
     *
     * @param string $method
     * @param string $expectedClass
     */
    public function testInstantiation($method, $expectedClass): void {
        $request = $this->getRequestMock();
        $options = $this->getOptionsMock();
        $request->method('parse')->willReturn($options);
        $request->method('getOptions')->willReturn($options);

        /** @var Factory|PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMockBuilder(Factory::class)
            ->setConstructorArgs([$request])
            ->setMethods(['getSourcesList'])
            ->getMock();
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
            ['getComposerCommand', ComposerCommand::class],
            ['getStatusCommand', StatusCommand::class],
            ['getSelfupdateCommand', SelfupdateCommand::class],
        ];
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function getRequestMock() {
        return $this->createMock(Request::class);
    }

    /**
     * @return Cli\Options|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Cli\Options::class);
    }
}
