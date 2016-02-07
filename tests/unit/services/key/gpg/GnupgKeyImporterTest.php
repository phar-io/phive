<?php
namespace PharIo\Phive;

class GnupgKeyImporterTest extends \PHPUnit_Framework_TestCase {

    public function testImport() {
        $gnupg = $this->prophesize(\Gnupg::class);
        $gnupg->import('foo')->shouldBeCalled();
        $importer = new GnupgKeyImporter($gnupg->reveal());
        $importer->importKey('foo');
    }

}




