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
 * @covers \PharIo\Phive\PhiveVersion
 * @covers \PharIo\Phive\StaticPhiveVersion
 */
class StaticPhiveVersionTest extends TestCase {
    public function testGetVersionString(): void {
        $version = new StaticPhiveVersion('4.2.1');

        $this->assertStringContainsString('4.2.1', $version->getVersionString());
    }

    public function testGetVersion(): void {
        $version = new StaticPhiveVersion('0.3.1');

        $this->assertSame('0.3.1', $version->getVersion());
    }
}
