<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ComposerAlias
 */
class ComposerAliasTest extends TestCase {
    public function testThrowsExceptionIfAliasDoesNotContainASlash(): void {
        $this->expectException(\InvalidArgumentException::class);
        new ComposerAlias('foo');
    }

    public function testThrowsExceptionIfAliasStartsWithASlash(): void {
        $this->expectException(\InvalidArgumentException::class);
        new ComposerAlias('/foo');
    }

    public function testToString(): void {
        $alias = new ComposerAlias('some/alias');
        $this->assertSame('some/alias', $alias->asString());
    }

    public function testGetVendorReturnsExpectedValue(): void {
        $alias = new ComposerAlias('some/alias');
        $this->assertSame('some', $alias->getVendor());
    }

    public function testGetNameReturnsExpectedValue(): void {
        $alias = new ComposerAlias('some/alias');
        $this->assertSame('alias', $alias->getName());
    }
}
