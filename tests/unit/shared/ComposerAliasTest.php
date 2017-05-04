<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ComposerAlias
 */
class ComposerAliasTest extends TestCase {

    public function testThrowsExceptionIfAliasDoesNotContainASlash() {
        $this->expectException(\InvalidArgumentException::class);
        new ComposerAlias('foo');
    }

    public function testThrowsExceptionIfAliasStartsWithASlash() {
        $this->expectException(\InvalidArgumentException::class);
        new ComposerAlias('/foo');
    }

    public function testToString() {
        $alias = new ComposerAlias('some/alias');
        $this->assertSame('some/alias', $alias->__toString());
    }

    public function testGetVendorReturnsExpectedValue() {
        $alias = new ComposerAlias('some/alias');
        $this->assertSame('some', $alias->getVendor());
    }

    public function testGetNameReturnsExpectedValue() {
        $alias = new ComposerAlias('some/alias');
        $this->assertSame('alias', $alias->getName());
    }

}
