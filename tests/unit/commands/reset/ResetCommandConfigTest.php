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

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\ResetCommandConfig
 */
class ResetCommandConfigTest extends TestCase {
    public function testGetAliases(): void {
        $options = $this->getOptionsMock();
        $options->method('getArgumentCount')
            ->willReturn(3);
        $options->method('getArguments')
            ->willReturn(['foo', 'bar', 'baz']);

        $config = new ResetCommandConfig($options);

        $expected = ['foo', 'bar', 'baz'];
        $this->assertEquals($expected, $config->getAliases());
    }

    public function testHasAliasesReturnsTrueIfOptionsHaveArguments(): void {
        $options = $this->getOptionsMock();
        $options->method('getArgumentCount')
            ->willReturn(2);

        $config = new ResetCommandConfig($options);

        $this->assertTrue($config->hasAliases());
    }

    public function testHasAliasesReturnsFalseIfOptionsHaveNoArguments(): void {
        $options = $this->getOptionsMock();
        $options->method('getArgumentCount')
            ->willReturn(0);

        $config = new ResetCommandConfig($options);

        $this->assertFalse($config->hasAliases());
    }

    /**
     * @return Cli\Options|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Cli\Options::class);
    }
}
