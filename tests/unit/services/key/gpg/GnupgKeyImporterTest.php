<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

class GnupgKeyImporterTest extends TestCase {

    public function testImport() {
        $gnupg = $this->prophesize(\Gnupg::class);
        $gnupg->import('foo')->shouldBeCalled();
        $importer = new GnupgKeyImporter($gnupg->reveal());
        $importer->importKey('foo');
    }

}




