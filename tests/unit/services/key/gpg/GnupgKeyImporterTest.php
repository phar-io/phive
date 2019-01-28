<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

class GnupgKeyImporterTest extends TestCase {
    public function testImport(): void {
        $gnupg = $this->prophesize(\Gnupg::class);
        $gnupg->import('foo')->shouldBeCalled();
        $importer = new GnupgKeyImporter($gnupg->reveal());
        $importer->importKey('foo');
    }
}
