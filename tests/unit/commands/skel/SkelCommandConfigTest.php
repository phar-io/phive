<?php declare(strict_types = 1);
namespace PharIo\Phive;

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
        $config = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
        $this->assertEquals('/tmp/phive.xml', $config->getDestination());
    }

    public function testGetTemplateFilename(): void {
        $config   = new SkelCommandConfig($this->cliOptionsProphecy->reveal(), '/tmp/');
        $expected = \realpath(__DIR__ . '/../../../../conf/phive.skeleton.xml');
        $actual   = \realpath($config->getTemplateFilename());
        $this->assertEquals($expected, $actual);
    }
}
