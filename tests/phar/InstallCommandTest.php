<?php
namespace PharIo\Phive\PharRegressionTests;

class InstallCommandTest extends PharTestCase {

    public function testInstallsPhar() {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $this->changeWorkingDirectory(__DIR__ . '/tmp');
        $this->runPhiveCommand('install', ['phpunit']);

        $this->assertFileExists(__DIR__ .'/tmp/tools/phpunit');
    }

}