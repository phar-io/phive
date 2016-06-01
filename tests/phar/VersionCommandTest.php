<?php
namespace PharIo\Phive\PharRegressionTests;

class VersionCommandTest extends PharTestCase {

    public function testOutput() {
        $result = $this->runPhiveCommand('version');
        $this->assertContains('Phive', $result);
        $this->assertContains('Copyright', $result);
    }

}