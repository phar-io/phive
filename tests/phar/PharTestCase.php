<?php
namespace PharIo\Phive\PharRegressionTests;

use PharIo\Phive\Directory;
use PharIo\Phive\File;
use PharIo\Phive\Filename;
use PharIo\Phive\Phar;
use PharIo\Phive\PharRegistry;
use PharIo\Phive\PhiveXmlConfig;
use PharIo\Phive\Version;
use PharIo\Phive\XmlFile;

class PharTestCase extends \PHPUnit_Framework_TestCase {

    private $pharSize = 0;

    /**
     * @var PharRegistry
     */
    private $registry;

    final protected function setUp() {
        $this->createCopyOfPharUnderTest();
        $this->createTemporaryDirectory();
        $this->_setUp();
    }

    final protected function tearDown() {
        $this->removeTemporaryDirectory();
        $this->ensurePharIsUnchanged();
        unlink($this->getTestedPharFilename());
        $this->_tearDown();
    }

    protected function _setUp() {

    }

    protected function _tearDown() {

    }

    /**
     * @param string $directory
     */
    protected function changeWorkingDirectory($directory) {
        chdir($directory);
    }

    /**
     * @param $command
     * @param array $arguments
     * @param array $switches
     *
     * @return mixed
     */
    protected function runPhiveCommand($command, array $arguments = [], array $switches = []) {
        $call = $this->getTestedPharFilename() . ' ' . $command;
        $call .= ' --home=' . __DIR__ . '/fixtures/phive-home';
        foreach ($switches as $switch) {
            $call .= ' -' . $switch;
        }
        foreach ($arguments as $argument) {
            $call .= ' ' . $argument;
        }
        $call .= ' 2>&1';
        @exec($call, $outputLines, $resultCode);

        $output = '';

        foreach ($outputLines as $line) {
            $output .= $line . "\n";
        }

        if ($resultCode !== 0) {
            throw new \RuntimeException($output);
        }

        return $output;
    }

    protected function addPharToRegistry($name, $version, $filename) {
        $this->getPharRegistry()->addPhar(
            new Phar($name, new Version($version), new File(new Filename($filename), 'foo'))
        );
    }

    /**
     * @return PhiveXmlConfig
     */
    protected function getPhiveXmlConfig() {
        return new PhiveXmlConfig(
            new XmlFile(
                new Filename(__DIR__ . '/tmp/phive.xml'),
                'https://phar.io/phive',
                'phive'
            )
        );
    }

    /**
     * @param string $filename
     * @param string $target
     */
    protected function assertSymlinkTargetEquals($filename, $target) {
        $this->assertEquals($target, readlink($filename));
    }

    /**
     * @param $path
     */
    private function removeDirectory($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);

        return;
    }

    private function ensurePharIsUnchanged() {
        if ($this->pharSize !== filesize($this->getTestedPharFilename())) {
            $this->fail('The PHAR under test was changed during the test!');
        }
    }

    /**
     * @return string
     */
    private function getTestedPharFilename() {
        return __DIR__  . '/under-test.php';
    }

    /**
     * @return string
     */
    private function getPharFilename() {
        return glob(__DIR__ . '/../../build/phar/*.phar')[0];
    }

    private function createTemporaryDirectory() {
        if (!file_exists(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    private function removeTemporaryDirectory() {
        if (file_exists(__DIR__ . '/tmp')) {
            $this->removeDirectory(__DIR__ . '/tmp');
        }
    }

    private function createCopyOfPharUnderTest() {
        $testedPharFilename = $this->getTestedPharFilename();
        copy($this->getPharFilename(), $testedPharFilename);
        chmod($testedPharFilename, 0777);
        $this->pharSize = filesize($testedPharFilename);
    }

    /**
     * @return PharRegistry
     */
    private function getPharRegistry() {
        if (null === $this->registry) {
            $xmlFilename = __DIR__ . '/fixtures/phive-home/phars.xml';
            if (file_exists($xmlFilename)) {
                unlink($xmlFilename);
            }
            $this->registry = new PharRegistry(
                new XmlFile(new Filename($xmlFilename), 'https://phar.io/phive/installdb', 'phars'),
                new Directory(__DIR__ . '/fixtures/phive-home/phars')
            );
        }
        return $this->registry;
    }
}