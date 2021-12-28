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

use PharIo\FileSystem\File;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PurgeCommand
 */
class PurgeCommandTest extends TestCase {
    public function testInvokesRepository(): void {
        $repository = $this->getPharRegistryMock();

        $phar1 = $this->getPharMock();
        $phar2 = $this->getPharMock();

        $repository->expects($this->once())
            ->method('getUnusedPhars')
            ->willReturn([$phar1, $phar2]);

        $repository->expects($this->at(1))
            ->method('removePhar')
            ->with($phar1);

        $repository->expects($this->at(2))
            ->method('removePhar')
            ->with($phar2);

        $command = new PurgeCommand($repository, $this->getOutputMock());
        $command->execute();
    }

    public function testWritesToOutput(): void {
        $repository = $this->getPharRegistryMock();
        $output     = $this->getOutputMock();

        $phar1 = $this->getPharMock();

        $repository->expects($this->once())
            ->method('getUnusedPhars')
            ->willReturn([$phar1]);

        $output->expects($this->once())
            ->method('writeInfo');

        $command = new PurgeCommand($repository, $output);
        $command->execute();
    }

    /**
     * @return PharRegistry|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharRegistryMock() {
        return $this->createMock(PharRegistry::class);
    }

    /**
     * @return Phar|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharMock() {
        /** @var PHPUnit_Framework_MockObject_MockObject|Version $version */
        $version = $this->createMock(Version::class);
        /** @var File|PHPUnit_Framework_MockObject_MockObject $file */
        $file = $this->createMock(File::class);

        return new Phar('foo', $version, $file);
    }

    /**
     * @return Cli\Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputMock() {
        return $this->createMock(Cli\Output::class);
    }
}
