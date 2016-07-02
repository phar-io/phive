<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\Cli\Options
 */
class OptionsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Options
     */
    private $options;

    protected function setUp() {
        $this->options = new Options();
    }

    public function testHasNoArgumentsWhenCreated() {
        $this->assertCount(0, $this->options->getArgumentCount());
    }

    public function testAddedArgumentsCanBeRetrieved() {
        $this->options->addArgument('arg1');
        $this->options->addArgument('arg2');

        $this->assertCount(2, $this->options->getArgumentCount());
        $this->assertEquals(['arg1', 'arg2'], $this->options->getArguments());
        $this->assertEqual('arg2', $this->options->getArgument(1));
    }
}

