<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RegressionTestBootstrap {
    public function run(): void {
        print "Building PHAR... \n";

        \chdir(__DIR__ . '/../..');
        @\exec('ant phar', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Could not build PHAR');
        }

        $filename = \glob(__DIR__ . '/../../build/phar/*.phar')[0];
        print \sprintf("Using PHAR %s for the test run. \n\n", $filename);
    }
}
