<?php
namespace PharIo\Phive {

    use TheSeer\CLI\CommandOptions;

    /**
     * @covers PharIo\Phive\InstallCommandConfig
     */
    class InstallCommandConfigTest extends \PHPUnit_Framework_TestCase {

        use ScalarTestDataProvider;

        public function testGetWorkingDirectory() {
            $directory = $this->getDirectoryMock();
            $config = $this->getConfigMock();

            $config->expects($this->once())
                ->method('getWorkingDirectory')
                ->willReturn($directory);

            $commandConfig = new InstallCommandConfig($this->getOptionsMock(), $config);
            $this->assertSame($directory, $commandConfig->getWorkingDirectory());
        }

        /**
         * @dataProvider boolProvider
         *
         * @param $switch
         */
        public function testMakeCopy($switch) {
            $options = $this->getOptionsMock();
            $options->expects($this->once())
                ->method('isSwitch')
                ->with('copy')
                ->willReturn($switch);

            $commandConfig = new InstallCommandConfig($options, $this->getConfigMock());
            $this->assertSame($switch, $commandConfig->makeCopy());
        }

        public function testGetRequestedPharsFromConfig() {
            $options = $this->getOptionsMock();
            $options->expects($this->once())
                ->method('getArgumentCount')
                ->willReturn(0);

            $config = $this->getConfigMock();
            $config->expects($this->once())
                ->method('getPhars')
                ->willReturn(['foo']);

            $commandConfig = new InstallCommandConfig($options, $config);
            $this->assertEquals(['foo'], $commandConfig->getRequestedPhars());
        }

        public function testGetRequestedPharsFromCliOptions() {
            $options = $this->getOptionsMock();
            $options->expects($this->any())
                ->method('getArgumentCount')
                ->willReturn(3);

            $options->expects($this->any())
                ->method('getArgument')
                ->willReturnMap([
                    [0, 'https://example.com/foo.phar'],
                    [1, 'phpunit'],
                    [2, 'phpab@1.12.0']
                ]);

            $expected = [
                RequestedPhar::fromUrl(new Url('https://example.com/foo.phar')),
                RequestedPhar::fromAlias(new PharAlias('phpunit', new AnyVersionConstraint())),
                RequestedPhar::fromAlias(new PharAlias('phpab', new ExactVersionConstraint('1.12.0'))),
            ];

            $commandConfig = new InstallCommandConfig($options, $this->getConfigMock());
            $this->assertEquals($expected, $commandConfig->getRequestedPhars());
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|CommandOptions
         */
        private function getOptionsMock() {
            return $this->getMockBuilder(CommandOptions::class)
                ->disableOriginalConstructor()->getMock();
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|Config
         */
        private function getConfigMock() {
            return $this->getMockBuilder(Config::class)
                ->disableOriginalConstructor()->getMock();
        }

        /**
         * @return \PHPUnit_Framework_MockObject_MockObject|Directory
         */
        private function getDirectoryMock() {
            return $this->getMockBuilder(Directory::class)
                ->disableOriginalConstructor()->getMock();
        }

    }

}
