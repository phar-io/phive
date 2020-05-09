<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ListCommand
 */
class ListCommandTest extends TestCase {
    public function testWritesExpectedAliasesToOutput(): void {
        $sourcesList  = $this->getSourcesListMock();
        $localSources = $this->getSourcesListMock();
        $output       = $this->getOutputMock();

        $localSources->method('getAliases')
            ->willReturn(['phpdox']);

        $sourcesList->method('getAliases')
            ->willReturn(['phpunit', 'phpab', 'phploc']);

        $output->expects($this->at(1))
            ->method('writeText')
            ->with($this->stringContains('phpdox'));

        $output->expects($this->at(3))
            ->method('writeText')
            ->with($this->stringContains('phpunit'));

        $output->expects($this->at(4))
            ->method('writeText')
            ->with($this->stringContains('phpab'));

        $output->expects($this->at(5))
            ->method('writeText')
            ->with($this->stringContains('phploc'));

        $command = new ListCommand($sourcesList, $localSources, $output);
        $command->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SourcesList
     */
    private function getSourcesListMock() {
        return $this->createMock(SourcesList::class);
    }

    /**
     * @return Output|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputMock() {
        return $this->createMock(Output::class);
    }
}
