<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\PhiveContext
 */
class PhiveContextTest extends TestCase {

    public function testAcceptsArgumentsAndCanContinueReturnTrueIfNotOptionsDoNotHaveArguments() {
        $context = new PhiveContext();
        $this->assertTrue($context->acceptsArguments());
        $this->assertTrue($context->canContinue());
    }

    public function testAcceptsArgumentsAndCanContinueReturnFalseIfNotOptionsHaveArguments() {
        $context = new PhiveContext();
        $context->addArgument('foo');
        $this->assertFalse($context->acceptsArguments());
        $this->assertFalse($context->canContinue());
    }

    public function testKnowsHomeOptions() {
        $context = new PhiveContext();
        $this->assertTrue($context->knowsOption('home'));
    }

    /**
     * @dataProvider requiresValueTestDataProvider
     *
     * @param string $option
     * @param bool $expectedResult
     */
    public function testRequiresValueReturnsExpectedValue($option, $expectedResult) {
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
