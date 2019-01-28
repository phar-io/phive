<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\DefaultCommandConfig
 */
class DefaultCommandConfigTest extends TestCase {
    use ScalarTestDataProvider;

    /**
     * @dataProvider boolProvider
     *
     * @param bool $hasOption
     */
    public function testHasVersionOptionReturnsExpectedResult($hasOption): void {
        $options = $this->getOptionsMock();
        $options->method('hasOption')->with('version')->willReturn($hasOption);

        $config = new DefaultCommandConfig($options);

        $this->assertSame($hasOption, $config->hasVersionOption());
    }

    /**
     * @return Options|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }
}
