<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HelpCommand
 */
class HelpCommandTest extends TestCase {

    public function testWritesExpectedTextToOutput() {
        $output = $this->getOutputMock();
        $output->expects($this->once())
            ->method('writeText')
            ->with($this->stringContains('help'));

        $command = new HelpCommand(
            $this->getEnvironmentMock(),
            $output
        );

        $command->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Environment
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Output
     */
    protected function getOutputMock() {
        return $this->createMock(Output::class);
    }

}
