<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\GnuPG
 */
class GnuPGTest extends \PHPUnit_Framework_TestCase {

    public function testImportReturnsExpectedArray() {
        $executable = $this->getFilenameMock();
        $homeDirectory = $this->getDirectoryMock();

        $key = 'SomeKey';
        $status = 'IMPORT_OK 1 foo';

        $pipeIO = $this->getPipeIOMock();
        $pipeIO->expects($this->once())
            ->method('writeToPipe')
            ->with(PipeIO::PIPE_STDIN, $key);
        $pipeIO->method('readFromStatus')->willReturn($status);

        $expected  = [
            'imported' => 1,
            'fingerprint' => 'foo'
        ];

        $gnupg = new GnuPG($executable, $homeDirectory, $pipeIO);
        $this->assertEquals($expected, $gnupg->import($key));
    }

    public function testImportReturnsExpectedArrayWhenImportFails() {
        $executable = $this->getFilenameMock();
        $homeDirectory = $this->getDirectoryMock();

        $key = 'SomeKey';
        $status = 'ERROR';

        $pipeIO = $this->getPipeIOMock();
        $pipeIO->expects($this->once())
            ->method('writeToPipe')
            ->with(PipeIO::PIPE_STDIN, $key);
        $pipeIO->method('readFromStatus')->willReturn($status);

        $expected  = ['imported' => 0];

        $gnupg = new GnuPG($executable, $homeDirectory, $pipeIO);
        $this->assertEquals($expected, $gnupg->import($key));
    }

    /**
     * @dataProvider verificationStatusProvider
     *
     * @param string $status
     * @param string $expectedFingerprint
     * @param int $expectedValidity
     * @param string $expectedTimestamp
     * @param int $expectedSummary
     */
    public function testVerifyReturnsExpectedArray(
        $status, $expectedFingerprint, $expectedValidity, $expectedTimestamp, $expectedSummary
    ) {
        $executable = $this->getFilenameMock();
        $homeDirectory = $this->getDirectoryMock();

        $message = 'Some Message';
        $signature = 'Some Signature';
        $pipeIO = $this->getPipeIOMock();
        $pipeIO->method('readFromStatus')->willReturn($status);

        $gnupg = new GnuPG($executable, $homeDirectory, $pipeIO);

        $expected = [
            [
                'fingerprint' => $expectedFingerprint,
                'validity'    => $expectedValidity,
                'timestamp'   => $expectedTimestamp,
                'status'      => $status,
                'summary'     => $expectedSummary
            ]
        ];

        $actual = $gnupg->verify($message, $signature);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider invalidVerificationStatusProvider
     *
     * @param string $status
     */
    public function testVerifyReturnsFalseOnInvalidResult($status) {
        $executable = $this->getFilenameMock();
        $homeDirectory = $this->getDirectoryMock();

        $message = 'Some Message';
        $signature = 'Some Signature';
        $pipeIO = $this->getPipeIOMock();
        $pipeIO->method('readFromStatus')->willReturn($status);

        $gnupg = new GnuPG($executable, $homeDirectory, $pipeIO);

        $actual = $gnupg->verify($message, $signature);

        $this->assertFalse($actual);
    }


    public static function verificationStatusProvider() {
        return [
            'Valid Signature' => [
                '[GNUPG:] VALIDSIG SomeFingerprint 2015-04-28 1461855515',
                'SomeFingerprint',
                0,
                '1461855515',
                0
            ],
            'Bad Signature' => [
                '[GNUPG:] BADSIG 4AA394086372C20A Sebastian Bergmann <sb@sebastian-bergmann.de>',
                '4AA394086372C20A',
                0,
                0,
                4
            ],
            'Signature Error' => [
                '[GNUPG:] ERRSIG 4AA394086372C20A 1 10 00 1405769272 9',
                '4AA394086372C20A',
                0,
                1405769272,
                128
            ]
        ];
    }

    public function invalidVerificationStatusProvider() {
        return [
            ['foo'],
            ['[GNUPG:] FOO'],
            ['[GNUPG:] FOO BAR BAZ']
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filename
     */
    private function getFilenameMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Filename::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PipeIO
     */
    private function getPipeIOMock() {
        return $this->getMockWithoutInvokingTheOriginalConstructor(PipeIO::class);
    }

}
