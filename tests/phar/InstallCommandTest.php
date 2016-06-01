<?php
namespace PharIo\Phive\PharRegressionTests;

class InstallCommandTest extends PharTestCase {

    public function testInstallsPhar() {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $this->changeWorkingDirectory(__DIR__ . '/tmp');
        $this->runPhiveCommand('install', ['phpunit@5.3.1']);

        $this->assertSymlinkTargetEquals(__DIR__ .'/tmp/tools/phpunit', __DIR__ . '/fixtures/phive-home/phars/phpunit-5.3.1.phar');
    }

}