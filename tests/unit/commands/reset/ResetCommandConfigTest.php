<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ResetCommandConfig
 */
class ResetCommandConfigTest extends TestCase {

    public function testGetAliases() {
        $options = $this->getOptionsMock();
        $options->method('getArgumentCount')
            ->willReturn(3);
        $options->method('getArguments')
            ->willReturn(['foo', 'bar', 'baz']);

        $config = new ResetCommandConfig($options);

        $expected = ['foo', 'bar', 'baz'];
        $this->assertEquals($expected, $config->getAliases());
    }

    public function testHasAliasesReturnsTrueIfOptionsHaveArguments() {
        $options = $this->getOptionsMock();
        $options->method('getArgumentCount')
            ->willReturn(2);

        $config = new ResetCommandConfig($options);

        $this->assertTrue($config->hasAliases());
    }

    public function testHasAliasesReturnsFalseIfOptionsHaveNoArguments() {
        $options = $this->getOptionsMock();
        $options->method('getArgumentCount')
            ->willReturn(0);

        $config = new ResetCommandConfig($options);

        $this->assertFalse($config->hasAliases());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cli\Options
     */
    private function getOptionsMock() {
        return $this->createMock(Cli\Options::class);
    }

}
