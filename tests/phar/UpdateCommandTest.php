<?php
namespace PharIo\Phive\PharRegressionTests;

class UpdateCommandTest extends PharTestCase {

    public function testUpdatesSymlinkToUpdatedVersion() {
        copy(__DIR__  . '/fixtures/updateCommandTest/phive.xml', __DIR__ . '/tmp/phive.xml');

        mkdir(__DIR__ . '/tmp/tools');
        symlink(__DIR__ . '/fixtures/phive-home/phars/phpunit-5.3.1.phar', __DIR__ . '/tmp/tools/phpunit');

        $this->addPharToRegistry('phpunit', '5.3.4', 'phpunit-5.3.4.phar');
        $this->changeWorkingDirectory(__DIR__ . '/tmp');
        $this->runPhiveCommand('update');

        $this->assertSymlinkTargetEquals(__DIR__ . '/tmp/tools/phpunit', __DIR__ .'/fixtures/phive-home/phars/phpunit-5.3.4.phar');
    }

}