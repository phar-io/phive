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

/**
 * @covers \PharIo\Phive\PhiveContext
 */
class PhiveContextTest extends TestCase {
    public function testAcceptsArgumentsAndCanContinueReturnTrueIfNotOptionsDoNotHaveArguments(): void {
        $context = new PhiveContext();
        $this->assertTrue($context->acceptsArguments());
        $this->assertTrue($context->canContinue());
    }

    public function testAcceptsArgumentsAndCanContinueReturnFalseIfNotOptionsHaveArguments(): void {
        $context = new PhiveContext();
        $context->addArgument('foo');
        $this->assertFalse($context->acceptsArguments());
        $this->assertFalse($context->canContinue());
    }

    public function testKnowsHomeOptions(): void {
        $context = new PhiveContext();
        $this->assertTrue($context->knowsOption('home'));
    }

    /**
     * @dataProvider requiresValueTestDataProvider
     *
     * @param string $option
     * @param bool   $expectedResult
     */
    public function testRequiresValueReturnsExpectedValue($option, $expectedResult): void {
        $context = new PhiveContext();
        $this->assertSame($expectedResult, $context->requiresValue($option));
    }

    public function requiresValueTestDataProvider() {
        return [
            ['home', true],
            ['foo', false],
            ['home2', false]
        ];
    }
}
