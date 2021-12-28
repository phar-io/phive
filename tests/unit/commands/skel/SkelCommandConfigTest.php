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

use function realpath;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\SkelCommandConfig
 */
class SkelCommandConfigTest extends TestCase {
    /** @var CLI\Options|ObjectProphecy */
    private $cliOptionsProphecy;

    protected function setUp(): void {
        $this->cliOptionsProphecy = $this->prophesize(CLI\Options::class);
    }

    /**
     * @dataProvider allowOverwriteProvider
     *
     * @param bool $switch
     */
    public function testAllowOverwrite($switch): void {
        $this->cliOptionsProphecy->hasOption('force')->willReturn($switch);
        $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');

        $this->assertSame($switch, $config->allowOverwrite());
    }

    public function allowOverwriteProvider() {
        return [
            [true],
            [false]
        ];
    }

    public function testGetDestination(): void {
        $this->cliOptionsProphecy->hasOption('auth')->willReturn(false);
        $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
        $this->assertEquals('/tmp/.phive/phars.xml', $config->getDestination());

        $this->cliOptionsProphecy->hasOption('auth')->willReturn(true);
        $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
        $this->assertEquals('/tmp/.phive/auth.xml', $config->getDestination());
    }

    public function testGetTemplateFilename(): void {
        $this->cliOptionsProphecy->hasOption('auth')->willReturn(false);
        $config   = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
        $expected = realpath(__DIR__ . '/../../../../conf/phive.skeleton.xml');
        $actual   = realpath($config->getTemplateFilename());
        $this->assertEquals($expected, $actual);

        $this->cliOptionsProphecy->hasOption('auth')->willReturn(true);
        $config   = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
        $expected = realpath(__DIR__ . '/../../../../conf/auth.skeleton.xml');
        $actual   = realpath($config->getTemplateFilename());
        $this->assertEquals($expected, $actual);
    }
}
