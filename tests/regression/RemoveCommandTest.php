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

class RemoveCommandTest extends RegressionTestCase {
    public function testRemovesSymlink(): void {
        $this->addPharToRegistry('phpunit', '5.3.1', 'phpunit-5.3.1.phar', $this->getToolsDirectory()->file('phpunit'));
        $this->usePhiveXmlConfig(__DIR__ . '/fixtures/removeCommandTest/phive.xml');
        $this->createSymlink(
            $this->getPhiveHomeDirectory()->child('phars')->file('phpunit-5.3.1.phar')->asString(),
            $this->getToolsDirectory()->file('phpunit')->asString()
        );

        $this->runPhiveCommand('remove', ['phpunit']);

        $this->assertFileNotExists($this->getToolsDirectory()->file('phpunit')->asString());
    }
}
