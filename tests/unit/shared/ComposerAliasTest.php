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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ComposerAlias
 */
class ComposerAliasTest extends TestCase {
    public function testThrowsExceptionIfAliasDoesNotContainASlash(): void {
        $this->expectException(InvalidArgumentException::class);
        new ComposerAlias('foo');
    }

    public function testThrowsExceptionIfAliasStartsWithASlash(): void {
        $this->expectException(InvalidArgumentException::class);
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
