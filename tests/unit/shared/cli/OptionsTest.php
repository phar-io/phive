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

use PharIo\Phive\Cli\CommandOptionsException;
use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Cli\Options
 */
class OptionsTest extends TestCase {
    /** @var Options */
    private $options;

    protected function setUp(): void {
        $this->options = new Options();
    }

    public function testHasNoArgumentsWhenCreated(): void {
        $this->assertEquals(0, $this->options->getArgumentCount());
    }

    public function testAddedArgumentsCanBeRetrieved(): void {
        $this->options->addArgument('arg1');
        $this->options->addArgument('arg2');

        $this->assertEquals(2, $this->options->getArgumentCount());
        $this->assertEquals(['arg1', 'arg2'], $this->options->getArguments());
        $this->assertEquals('arg2', $this->options->getArgument(1));
    }

    public function testAccessingNonExistingArgumentThrowsException(): void {
        $this->expectException(CommandOptionsException::class);
        $this->expectExceptionCode(CommandOptionsException::InvalidArgumentIndex);
        $this->options->getArgument(2);
    }

    public function testOptionCanBeSet(): void {
        $this->options->setOption('opt1', 'val1');
        $this->assertTrue($this->options->hasOption('opt1'));
    }

    public function testOptionCanBeRetrieved(): void {
        $this->options->setOption('opt1', 'val1');
        $this->options->setOption('opt2', 'val2');
        $this->options->setOption('opt3', true);
        $this->assertEquals('val1', $this->options->getOption('opt1'));
        $this->assertEquals('val2', $this->options->getOption('opt2'));
        $this->assertTrue($this->options->getOption('opt3'));
    }

    public function testAccessingNonExistingOptionThrowsException(): void {
        $this->expectException(CommandOptionsException::class);
        $this->expectExceptionCode(CommandOptionsException::NoSuchOption);
        $this->options->getOption('not-existing');
    }

    public function testOptionsCanBeMerged(): void {
        $source = new Options();
        $source->addArgument('arg1');
        $source->setOption('opt1', 'val1');

        $result = $this->options->mergeOptions($source);

        $this->assertEquals(0, $result->getArgumentCount());
        $this->assertTrue($result->hasOption('opt1'));
        $this->assertEquals('val1', $result->getOption('opt1'));
    }
}
