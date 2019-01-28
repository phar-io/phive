<?php declare(strict_types = 1);
namespace PharIo\Phive\RegressionTests;

class VersionCommandTest extends RegressionTestCase {
    public function testOutput(): void {
        $result = $this->runPhiveCommand('version');
        $this->assertContains('Phive', $result);
        $this->assertContains('Copyright', $result);
    }
}
