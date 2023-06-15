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

use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

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
            ->with($this->stringContains('phpab'));

        $output->expects($this->at(4))
            ->method('writeText')
            ->with($this->stringContains('phploc'));

        $output->expects($this->at(5))
            ->method('writeText')
            ->with($this->stringContains('phpunit'));

        $command = new ListCommand($sourcesList, $localSources, $output);
        $command->execute();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|SourcesList
     */
    private function getSourcesListMock() {
        return $this->createMock(SourcesList::class);
    }

    /**
     * @return Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputMock() {
        return $this->createMock(Output::class);
    }
}
