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

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\DefaultCommand
 */
class DefaultCommandTest extends TestCase {
    public function testExecutesVersionCommandIfCorrespondingOptionIsPresent(): void {
        $versionCommand = $this->getVersionCommandMock();
        $helpCommand    = $this->getHelpCommandMock();
        $config         = $this->getDefaultCommandConfigMock();
        $command        = new DefaultCommand($versionCommand, $helpCommand, $config);

        $config->method('hasVersionOption')->willReturn(true);

        $versionCommand->expects($this->once())->method('execute');
        $helpCommand->expects($this->never())->method('execute');

        $command->execute();
    }

    public function testExecutesHelpCommandIfVersionOptionIsNotPresent(): void {
        $versionCommand = $this->getVersionCommandMock();
        $helpCommand    = $this->getHelpCommandMock();
        $config         = $this->getDefaultCommandConfigMock();
        $command        = new DefaultCommand($versionCommand, $helpCommand, $config);

        $config->method('hasVersionOption')->willReturn(false);

        $versionCommand->expects($this->never())->method('execute');
        $helpCommand->expects($this->once())->method('execute');

        $command->execute();
    }

    /**
     * @return DefaultCommandConfig|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDefaultCommandConfigMock() {
        return $this->createMock(DefaultCommandConfig::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|VersionCommand
     */
    private function getVersionCommandMock() {
        return $this->createMock(VersionCommand::class);
    }

    /**
     * @return HelpCommand|PHPUnit_Framework_MockObject_MockObject
     */
    private function getHelpCommandMock() {
        return $this->createMock(HelpCommand::class);
    }
}
