<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

trait MigrationMocks {
    private function getFileExistsMock(TestCase $testCase): Filename {
        $file = $testCase->createMock(Filename::class);
        $file->method('exists')->willReturn(true);

        return $file;
    }

    private function getFileMissingMock(TestCase $testCase): Filename {
        $file = $testCase->createMock(Filename::class);
        $file->method('exists')->willReturn(false);

        return $file;
    }

    private function getOutputMock(TestCase $testCase): Cli\Output {
        return $testCase->createMock(Cli\Output::class);
    }

    private function getDirectoryWithFileMock(TestCase $testCase, array $files): Directory {
        $directory = $testCase->createMock(Directory::class);
        $directory->method('file')->willReturnCallback(function ($file) use ($files, $testCase) {
            return \in_array($file, $files, true) ? $this->getFileExistsMock($testCase) : $this->getFileMissingMock($testCase);
        });

        return $directory;
    }

    private function getInputMock(TestCase $testCase, bool $accepted): Cli\Input {
        $input = $testCase->createMock(Cli\Input::class);
        $input->method('confirm')->willReturn($accepted);

        return $input;
    }

    private function getOptionsMock(TestCase $testCase): Cli\Options {
        return $testCase->createMock(Cli\Options::class);
    }
}
