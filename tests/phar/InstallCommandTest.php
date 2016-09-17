<?php
namespace PharIo\Phive\PharRegressionTests;

use PharIo\Phive\AnyVersionConstraint;
use PharIo\Phive\Cli\Runner;
use PharIo\Phive\ConfiguredPhar;
use PharIo\Phive\ExactVersionConstraint;
use PharIo\Phive\Filename;
use PharIo\Phive\InstalledPhar;
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