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
namespace PharIo\Phive\RegressionTests;

class PurgeCommandTest extends RegressionTestCase {
    public function testDeletesPurgeablePhar(): void {
        $this->addPharToRegistry('phpunit', '5.3.4', 'phpunit-5.3.4.phar');

        $this->assertTrue(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.4.phar')->exists()
        );

        $this->runPhiveCommand('purge');

        $this->assertFalse(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.4.phar')->exists()
        );
    }
}
