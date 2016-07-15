<?php
namespace PharIo\Phive\PharRegressionTests;

use PharIo\Phive\ConfiguredPhar;
use PharIo\Phive\ExactVersionConstraint;
use PharIo\Phive\PharAlias;
use PharIo\Phive\RequestedPharAlias;
use PharIo\Phive\Version;

class InstallCommandTest extends PharTestCase {

    public function testInstallsPhar() {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $this->runPhiveCommand('install', ['phpunit@5.3.1']);

        $this->assertSymlinkTargetEquals(
            $this->getToolsDirectory()->file('phpunit'),
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar')->asString()
        );
    }

    public function testAddsPharNodeToPhiveXmlConfig() {
        $phiveXmlConfig = $this->getPhiveXmlConfig();
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $this->assertEmpty($phiveXmlConfig->getPhars());

        $this->runPhiveCommand('install', ['phpunit@5.3.1']);

        // config needs to be reloaded
        $phiveXmlConfig = $this->getPhiveXmlConfig();

        $expectedPhars = [
            new ConfiguredPhar('phpunit', new ExactVersionConstraint('5.3.1'), new Version('5.3.1'))
        ];

        $this->assertEquals($expectedPhars, $phiveXmlConfig->getPhars());

    }

}