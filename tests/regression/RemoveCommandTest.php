<?php declare(strict_types = 1);
namespace PharIo\Phive\RegressionTests;

class RemoveCommandTest extends RegressionTestCase {
    public function testRemovesSymlink(): void {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar', $this->getToolsDirectory()->file('phpunit'));
        $this->usePhiveXmlConfig(__DIR__ . '/fixtures/removeCommandTest/phive.xml');
        $this->createSymlink(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar')->asString(),
            $this->getToolsDirectory()->file('phpunit')
        );

        $this->runPhiveCommand('remove', ['phpunit']);

        $this->assertFileNotExists($this->getToolsDirectory()->file('phpunit')->asString());
    }
}
