<?php
namespace PharIo\Phive\RegressionTests;

use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Runner;
use PharIo\Phive\ConfiguredPhar;
use PharIo\Phive\InstalledPhar;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PharIo\Version\Version;

class InstallCommandTest extends RegressionTestCase {

    public function testInstallsPhar() {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $this->runPhiveCommand('install', ['phpunit@5.3.1']);

        $this->assertSymlinkTargetEquals(
            $this->getToolsDirectory()->file('phpunit'),
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar')->asString()
        );
    }

    public function testCopiesPhar() {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $this->runPhiveCommand('install', ['--copy', 'phpunit@5.3.1']);

        $target = $this->getToolsDirectory()->file('phpunit')->asString();

        $this->assertFileIsNotASymlink($target);

        $this->assertFileEquals(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar')->asString(),
            $target
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
            new ConfiguredPhar('phpunit', new ExactVersionConstraint('5.3.1'), new Version('5.3.1'), new Filename('./tools/phpunit'))
        ];

        $this->assertEquals($expectedPhars, $phiveXmlConfig->getPhars());
    }

    public function testThrowsErrorIfGlobalAndTargetOptionsAreCombined() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(Runner::RC_PARAM_ERROR);

        $this->runPhiveCommand('install', ['--global', '--target tools', 'phpunit']);
    }

    public function testLinksPharToLocationConfiguredInPhiveXml()
    {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar');

        $phiveXmlConfig = $this->getPhiveXmlConfig();
        $phiveXmlConfig->addPhar(
            new InstalledPhar(
                'phpunit',
                new Version('5.3.1'),
                new AnyVersionConstraint(),
                new Filename('./foo/tests')
            )
        );

        $this->runPhiveCommand('install');

        $this->assertFileNotExists($this->getWorkingDirectory()->child('tools')->file('phpunit')->asString());
        $this->assertFileExists($this->getWorkingDirectory()->child('foo')->file('tests')->asString());
    }

}
