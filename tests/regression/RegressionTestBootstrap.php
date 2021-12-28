<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function chdir;
use function exec;
use function glob;
use function sprintf;
use RuntimeException;

class RegressionTestBootstrap {
    public function run(): void {
        print "Building PHAR... \n";

        chdir(__DIR__ . '/../..');
        @exec('ant phar', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new RuntimeException('Could not build PHAR');
        }

        $filename = glob(__DIR__ . '/../../build/phar/*.phar')[0];
        print sprintf("Using PHAR %s for the test run. \n\n", $filename);
    }
}
