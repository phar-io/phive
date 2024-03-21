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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\CommandLocator
 */
class CommandLocatorTest extends TestCase {
    /**
     * @dataProvider commandProvider
     *
     * @throws Cli\CommandLocatorException
     */
    public function testValidCommandsAreReturned($command, $factoryMethod): void {
        /** @var Factory|MockObject $factory */
        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())->method($factoryMethod);

        (new CommandLocator($factory))->getCommand($command);
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

    public function testRequestingAnUnknownCommandThrowsException(): void {
        $factory = $this->prophesize(Factory::class);
        $locator = new CommandLocator($factory->reveal());

        $this->expectException(Cli\CommandLocatorException::class);
        $this->expectExceptionCode(Cli\CommandLocatorException::UnknownCommand);

        $locator->getCommand('unknown');
    }
}
