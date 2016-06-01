<?php
namespace PharIo\Phive;

class PharTestBootstrap {

    public function run() {
        echo "Building PHAR... \n";

        chdir(__DIR__ . '/../..');
        @exec('ant phar', $output ,$returnCode);
        if ($returnCode !== 0) {
            throw new \RuntimeException('Could not build PHAR');
        }

        $filename = glob(__DIR__ . '/../../build/phar/*.phar')[0];
        echo sprintf("Using PHAR %s for the test run. \n\n", $filename);
    }
    
}