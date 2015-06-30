<?php
namespace TheSeer\Phive {

    class GnupgKeyImporterTest extends \PHPUnit_Framework_TestCase {

        public function testImport() {
            $gnupg = $this->prophesize(\Gnupg::class);
            $gnupg->import('foo')->shouldBeCalled();
            $importer = new NativeGnupgKeyImporter($gnupg->reveal());
            $importer->importKey('foo');
        }

    }

}


