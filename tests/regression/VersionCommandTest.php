<?php
namespace PharIo\Phive\RegressionTests;

class VersionCommandTest extends RegressionTestCase {

    public function testOutput() {
        $result = $this->runPhiveCommand('version');
        $this->assertContains('Phive', $result);
        $this->assertContains('Copyright', $result);
    }

}