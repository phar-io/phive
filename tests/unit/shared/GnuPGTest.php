<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\GnuPG
 */
class GnuPGTest extends TestCase {

    /**
     * @dataProvider importExecutionResultProvider
     *
     * @param $executionOutput
     * @param array $expectedResult
     */
    public function testImportReturnsExpectedArray($executionOutput, array $expectedResult) {
        $executorResult = $this->getExecutorResultMock();
        $executorResult->method('getOutput')->willReturn($executionOutput);
        $executor = $this->getExecutorMock();
        $executor->method('execute')->willReturn($executorResult);

        $gpgBinary = $this->getFilenameMock();

        $tmpFile = $this->getFilenameMock();
        $tmpDirectory = $this->getDirectoryMock();
        $tmpDirectory->method('file')->willReturn($tmpFile);

        $homeDirectory = $this->getDirectoryMock();
        $gpg = new GnuPG($executor, $gpgBinary, $tmpDirectory, $homeDirectory);

        $actual = $gpg->import('someKey');

        $this->assertSame($expectedResult, $actual);
    }

    public static function importExecutionResultProvider() {
        return [
            [
                'executionOutput' => ['IMPORT_OK 1 someFingerprint'],
                'expectedResult' => [
                    'imported' => 1,
                    'fingerprint' => 'someFingerprint'
                ]
            ],
            [
                'executionOutput' => ['ERROR'],
                'expectedResult' => [
                    'imported' => 0
                ]
            ]
        ];
    }

     /**
     * @dataProvider verifyExecutionResultProvider
     *
     * @param $executionOutput
     * @param array|bool $expectedResult
     */
    public function testVerifyReturnsExpectedArray($executionOutput, $expectedResult) {
        $executorResult = $this->getExecutorResultMock();
        $executorResult->method('getOutput')->willReturn($executionOutput);
        $executor = $this->getExecutorMock();
        $executor->method('execute')->willReturn($executorResult);

        $gpgBinary = $this->getFilenameMock();

        $tmpFile = $this->getFilenameMock();
        $tmpDirectory = $this->getDirectoryMock();
        $tmpDirectory->method('file')->willReturn($tmpFile);

        $homeDirectory = $this->getDirectoryMock();
        $gpg = new GnuPG($executor, $gpgBinary, $tmpDirectory, $homeDirectory);

        $actual = $gpg->verify('someMessage', 'someSignature');

        $this->assertEquals($expectedResult, $actual);
    }

    public static function verifyExecutionResultProvider() {
        return [
            [
                'executionOutput' => [
                    'SomeUnimportantLine',
                    '[GNUPG:] VALIDSIG D8406D0D82947747A394072C20A 2014-07-19 1405769272 0 4 0 1 10 00 D8C20A'
                ],
                'expectedResult' => [
                    [
                        'fingerprint' => 'D8406D0D82947747A394072C20A',
                        'validity' => 0,
                        'timestamp' => '1405769272',
                        'status' => [
                            0 => 'SomeUnimportantLine',
                            1 => '[GNUPG:] VALIDSIG D8406D0D82947747A394072C20A 2014-07-19 1405769272 0 4 0 1 10 00 D8C20A'
                        ],
                        'summary' => 0
                    ]
                ]
            ],
            [
                'executionOutput' => ['[GNUPG:] BADSIG 4AA394086372C20A Sebastian Bergmann <sb@sebastian-bergmann.de>'],
                'expectedResult' => [
                    [
                        'fingerprint' => '4AA394086372C20A',
                        'validity' => 0,
                        'timestamp' => 0,
                        'status' => [
                            0 => '[GNUPG:] BADSIG 4AA394086372C20A Sebastian Bergmann <sb@sebastian-bergmann.de>'
                        ],
                        'summary' => 4
                    ]
                ]
            ],
            [
                'executionOutput' => ['[GNUPG:] ERRSIG 4AA394086372C20A 1 10 00 1405769272 9'],
                'expectedResult' => [
                    [
                        'fingerprint' => '4AA394086372C20A',
                        'validity' => 0,
                        'timestamp' => '1405769272',
                        'status' => [
                            0 => '[GNUPG:] ERRSIG 4AA394086372C20A 1 10 00 1405769272 9'
                        ],
                        'summary' => 128
                    ]
                ]
            ],
            [
                'executionOutput' => ['SOME ERROR'],
                'expectedResult' => false
            ]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ExecutorResult
     */
    private function getExecutorResultMock() {
        return $this->createMock(ExecutorResult::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filename
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Executor
     */
    private function getExecutorMock() {
        return $this->createMock(Executor::class);
    }



}
