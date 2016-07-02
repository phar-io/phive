<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

/**
 * @covers PharIo\Phive\PurgeCommand
 */
class PurgeCommandTest extends \PHPUnit_Framework_TestCase {

    public function testInvokesRepository() {
        $config = $this->getCommandConfigMock();
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

        $command = new PurgeCommand($config, $repository, $this->getOutputMock());
        $command->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PurgeCommandConfig
     */
    private function getCommandConfigMock() {
        return $this->createMock(PurgeCommandConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharRegistry
     */
    private function getPharRegistryMock() {
        return $this->createMock(PharRegistry::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Phar
     */
    private function getPharMock() {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Version $version */
        $version = $this->createMock(Version::class);
        /** @var \PHPUnit_Framework_MockObject_MockObject|File $file */
        $file = $this->createMock(File::class);
        return new Phar('foo', $version, $file);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Output
     */
    private function getOutputMock() {
        return $this->createMock(Cli\Output::class);
    }

    public function testWritesToOutput() {
        $config = $this->getCommandConfigMock();
        $repository = $this->getPharRegistryMock();
        $output = $this->getOutputMock();

        $phar1 = $this->getPharMock();

        $repository->expects($this->once())
            ->method('getUnusedPhars')
            ->willReturn([$phar1]);

        $output->expects($this->once())
            ->method('writeInfo');

        $command = new PurgeCommand($config, $repository, $output);
        $command->execute();
    }
}



