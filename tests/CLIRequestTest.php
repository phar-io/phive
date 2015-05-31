<?php
namespace TheSeer\Phive {

    class CLIRequestTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider cliCommandProvider
         *
         * @param array  $cliArguments
         * @param string $expectedCommand
         */
        public function testReturnsExpectedCommand(array $cliArguments, $expectedCommand) {
            $request = new CLIRequest($cliArguments);
            $this->assertSame($expectedCommand, $request->getCommand());
        }

        public function cliCommandProvider() {
            return [
                [['foo'], 'help'],
                [['foo', 'bar'], 'bar'],
                [[], null]
            ];
        }

        /**
         * @dataProvider cliCommandOptionsProvider
         *
         * @param array             $cliArguments
         * @param CLICommandOptions $expectedOptions
         */
        public function testReturnsExpectedCommandOptions(array $cliArguments, CLICommandOptions $expectedOptions) {
            $request = new CLIRequest($cliArguments);
            $this->assertEquals($expectedOptions, $request->getCommandOptions());
        }

        public function cliCommandOptionsProvider() {
            return [
                [[], new CLICommandOptions([])],
                [['foo'], new CLICommandOptions([])],
                [['foo', 'bar', 'baz'], new CLICommandOptions(['baz'])]
            ];
        }

    }

}

