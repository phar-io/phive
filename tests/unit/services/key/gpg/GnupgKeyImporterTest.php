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
 * @covers \PharIo\Phive\GnupgKeyImporter
 */
class GnupgKeyImporterTest extends TestCase {
    public function testImport(): void {
        $gnupg = $this->prophesize(Gnupg::class);
        $gnupg->import('foo')->shouldBeCalled();
        $importer = new GnupgKeyImporter($gnupg->reveal());
        $importer->importKey('foo');
    }
}
