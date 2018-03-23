<?php

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
    public function testHasVersionOptionReturnsExpectedResult($hasOption)
    {
        $options = $this->getOptionsMock();
        $options->method('hasOption')->with('version')->willReturn($hasOption);

        $config = new DefaultCommandConfig($options);

        $this->assertSame($hasOption, $config->hasVersionOption());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Options
     */
    private function getOptionsMock()
    {
        return $this->createMock(Options::class);
    }


}
