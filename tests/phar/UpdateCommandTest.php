<?php
namespace PharIo\Phive\PharRegressionTests;

class UpdateCommandTest extends PharTestCase {

    public function testUpdatesSymlinkToUpdatedVersion() {
        $this->addPharToRegistry('phpunit', '5.3.4', 'phpunit-5.3.4.phar');
        $this->addPharToRegistry('phpunit', '5.3.4', 'phpunit-5.3.4.phar');
        $this->usePhiveXmlConfig(__DIR__ . '/fixtures/updateCommandTest/phive.xml');

        $this->createSymlink(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar'),
            $this->getToolsDirectory()->file('phpunit')
        );

        $this->runPhiveCommand('update');

        $this->assertSymlinkTargetEquals(
            $this->getToolsDirectory()->file('phpunit'),
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.4.phar')
        );
    }

}
