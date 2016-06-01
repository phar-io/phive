<?php
namespace PharIo\Phive\PharRegressionTests;

class RemoveCommandTest extends PharTestCase {

    public function testRemovesSymlink() {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        file_put_contents(__DIR__ . '/tmp/phpunit', 'foo');

        $this->changeWorkingDirectory(__DIR__ . '/tmp');
        $this->runPhiveCommand('remove', ['phpunit']);

        $this->assertFileNotExists(__DIR__ .'/tmp/tools/phpunit');
    }

}