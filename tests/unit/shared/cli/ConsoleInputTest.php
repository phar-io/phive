<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\ConsoleInput;
use PharIo\Phive\Cli\Output;

/**
 * @covers PharIo\Phive\Cli\ConsoleInput
 */
class ConsoleInputTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    /**
     * @dataProvider confirmProvider
     *
     * @param string $inputString
     * @param bool $expectedResult
     */
    public function testConfirmReturnsTrue($inputString, $expectedResult) {
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

    /**
     * @dataProvider  capitalizedOptionProvider
     *
     * @param bool $default
     * @param string $expectedString
     */
    public function testCapitalizesExpectedOption($default, $expectedString) {
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

    public static function capitalizedOptionProvider() {
        return [
            [true, '[Y|n]'],
            [false, '[y|N]']
        ];
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $default
     */
    public function testReturnsDefaultOnEnter($default) {
        $output = $this->getOutputMock();

        $inputStream = fopen('php://memory', 'w+');
        fwrite($inputStream, '');
        rewind($inputStream);

        $input = new ConsoleInput($output, $inputStream);
        $this->assertSame($default, $input->confirm('foo?', $default));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Output
     */
    private function getOutputMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Output::class);
    }
    
}