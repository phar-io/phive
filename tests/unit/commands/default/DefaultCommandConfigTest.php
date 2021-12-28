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

use PharIo\Phive\Cli\Options;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

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
     * @return Options|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }
}
