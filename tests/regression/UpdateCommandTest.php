<?php declare(strict_types = 1);
namespace PharIo\Phive\RegressionTests;

class UpdateCommandTest extends RegressionTestCase {
    public function testUpdatesSymlinkToUpdatedVersion(): void {
        $this->addPharToRegistry('phpunit', '5.3.4', 'phpunit-5.3.4.phar');
        $this->addPharToRegistry('phpunit', '5.3.4', 'phpunit-5.3.4.phar');
        $this->usePhiveXmlConfig(__DIR__ . '/fixtures/updateCommandTest/phive.xml');

        $this->createSymlink(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar')->asString(),
            $this->getToolsDirectory()->file('phpunit')->asString()
        );

        $this->runPhiveCommand('update');

        $this->assertSymlinkTargetEquals(
            $this->getToolsDirectory()->file('phpunit')->asString(),
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.4.phar')->asString()
        );
    }
}
