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

use PharIo\Version\AnyVersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\PharAlias
 */
class PharAliasTest extends TestCase {
    use ScalarTestDataProvider;

    /**
     * @dataProvider stringProvider
     *
     * @param string $value
     */
    public function testValueHandling($value): void {
        $alias = new PharAlias($value, new AnyVersionConstraint(), new AnyVersionConstraint());
        $this->assertSame($value, $alias->asString());
    }
}
