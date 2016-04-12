<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\CommandOptionsException;
use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\Cli\Options
 */
class OptionsTest extends \PHPUnit_Framework_TestCase {

    public function testParsesSwitches() {
        $argv = [
            '-foo',
            '-bar',
            '--foobar',
            'baz'
        ];
        $options = new Options($argv);
        $this->assertTrue($options->isSwitch('foo'));
        $this->assertTrue($options->isSwitch('bar'));
        $this->assertFalse($options->isSwitch('foobar'));
        $this->assertFalse($options->isSwitch('baz'));
    }

    public function testParsesOptions() {
        $argv = [
            '-foo',
            '--option1=value1',
            '--option2', 
            'value2',
            'baz',
            '--option3'
        ];
        $options = new Options($argv);
        $this->assertFalse($options->hasOption('foo'));
        $this->assertFalse($options->hasOption('baz'));
        $this->assertTrue($options->hasOption('option1'));
        $this->assertSame('value1', $options->getOption('option1'));
        $this->assertTrue($options->hasOption('option2'));
        $this->assertSame('value2', $options->getOption('option2'));
        $this->assertTrue($options->hasOption('option3'));
        $this->assertSame(true, $options->getOption('option3'));
    }

    public function testThrowsExceptionIfTryingToGetInvalidOption() {
        $options = new Options([]);

        $this->expectException(CommandOptionsException::class);
        $options->getOption('foo');
    }

    public function testParsesArguments() {
        $argv = [
            '-foo',
            'bar',
            'baz'
        ];
        $options = new Options($argv);
        $this->assertSame(2, $options->getArgumentCount());
        $this->assertSame('bar', $options->getArgument(0));
        $this->assertSame('baz', $options->getArgument(1));
        $this->assertSame(['bar', 'baz'], $options->getArguments());
    }

    public function testThrowsExceptionIfTryingToGetInvalidArgument() {
        $argv = [
            '-foo',
            'bar'
        ];
        $options = new Options($argv);
        $this->expectException(CommandOptionsException::class);
        $options->getArgument(1);
    }
}
