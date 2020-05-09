<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\Output;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\HelpCommand
 */
class HelpCommandTest extends TestCase {
    public function testWritesExpectedTextToOutput(): void {
        $output = $this->getOutputMock();
        $output->expects($this->once())
            ->method('writeMarkdown')
            ->with($this->stringContains('help'));

        $command = new HelpCommand(
            $this->getEnvironmentMock(),
            $output
        );

        $command->execute();
    }

    /**
     * @return Output|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOutputMock() {
        return $this->createMock(Output::class);
    }

    /**
     * @return Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }
}
