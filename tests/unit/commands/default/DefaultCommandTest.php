<?php

namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\DefaultCommand
 */
class DefaultCommandTest extends TestCase {

    public function testExecutesVersionCommandIfCorrespondingOptionIsPresent()
    {
        $versionCommand = $this->getVersionCommandMock();
        $helpCommand = $this->getHelpCommandMock();
        $config = $this->getDefaultCommandConfigMock();
        $command = new DefaultCommand($versionCommand, $helpCommand, $config);

        $config->method('hasVersionOption')->willReturn(true);

        $versionCommand->expects($this->once())->method('execute');
        $helpCommand->expects($this->never())->method('execute');

        $command->execute();
    }

    public function testExecutesHelpCommandIfVersionOptionIsNotPresent()
    {
        $versionCommand = $this->getVersionCommandMock();
        $helpCommand = $this->getHelpCommandMock();
        $config = $this->getDefaultCommandConfigMock();
        $command = new DefaultCommand($versionCommand, $helpCommand, $config);

        $config->method('hasVersionOption')->willReturn(false);

        $versionCommand->expects($this->never())->method('execute');
        $helpCommand->expects($this->once())->method('execute');

        $command->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DefaultCommandConfig
     */
    private function getDefaultCommandConfigMock() {
        return $this->createMock(DefaultCommandConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|VersionCommand
     */
    private function getVersionCommandMock() {
        return $this->createMock(VersionCommand::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|HelpCommand
     */
    private function getHelpCommandMock() {
        return $this->createMock(HelpCommand::class);
    }

}
