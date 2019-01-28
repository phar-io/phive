<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

class OutputFactory {
    public function getConsoleOutput(bool $outputProgress): Output {
        return new ConsoleOutput(ConsoleOutput::VERBOSE_INFO, $outputProgress);
    }

    public function getColoredConsoleOutput(bool $outputProgress): Output {
        return new ColoredConsoleOutput(ConsoleOutput::VERBOSE_INFO, $outputProgress);
    }
}
