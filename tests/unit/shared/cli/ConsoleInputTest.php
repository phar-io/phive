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

use function fopen;
use function fwrite;
use function rewind;
use PharIo\Phive\Cli\ConsoleInput;
use PharIo\Phive\Cli\Output;
use PharIo\Phive\Cli\RunnerException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\Cli\ConsoleInput
 */
class ConsoleInputTest extends TestCase {
    use ScalarTestDataProvider;

    public static function confirmProvider() {
        return [
            ['y', true],
            ['Y', true],
            ['Y ', true],
            ['n', false],
            ['N', false],
            ['N ', false]
        ];
    }

    public static function capitalizedOptionProvider() {
        return [
            [true, '[Y|n]'],
            [false, '[y|N]']
        ];
    }

    /**
     * @dataProvider confirmProvider
     *
     * @param string $inputString
     * @param bool   $expectedResult
     */
    public function testConfirmReturnsTrue($inputString, $expectedResult): void {
        $output = $this->getOutputMock();
        $output->expects($this->once())
            ->method('writeText')
            ->with('foo? [Y|n] ');
        $inputStream = fopen('php://memory', 'w+');
        fwrite($inputStream, $inputString);
        rewind($inputStream);

        $input = new ConsoleInput($output, $inputStream);

        $this->assertSame($expectedResult, $input->confirm('foo?'));
    }

    /**
     * @dataProvider  capitalizedOptionProvider
     *
     * @param bool   $default
     * @param string $expectedString
     */
    public function testCapitalizesExpectedOption($default, $expectedString): void {
        $output = $this->getOutputMock();

        $output->expects($this->once())
            ->method('writeText')
            ->with($this->stringContains($expectedString));

        $inputStream = fopen('php://memory', 'w+');
        fwrite($inputStream, 'y');
        rewind($inputStream);

        $input = new ConsoleInput($output, $inputStream);
        $input->confirm('foo?', $default);
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $default
     */
    public function testReturnsDefaultOnEnter($default): void {
        $output = $this->getOutputMock();

        $inputStream = fopen('php://memory', 'w+');
        fwrite($inputStream, "\n");
        rewind($inputStream);

        $input = new ConsoleInput($output, $inputStream);
        $this->assertSame($default, $input->confirm('foo?', $default));
    }

    public function testOnNonInteractive(): void {
        $output = $this->getOutputMock();
        $output->expects($this->once())
            ->method('writeText')
            ->with('foo? [Y|n] ');

        /*
         * Emulate a non-interactive shell by not writing anything
         *
         * In a real shell it can be emulate with `echo -n | phive install ...`
         */
        $inputStream = fopen('php://memory', 'r');

        $input = new ConsoleInput($output, $inputStream);

        $this->expectException(RunnerException::class);
        $this->expectExceptionMessage('Needs tty to be able to confirm');

        $input->confirm('foo?');
    }

    /**
     * @return Output|PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputMock() {
        return $this->createMock(Output::class);
    }
}
