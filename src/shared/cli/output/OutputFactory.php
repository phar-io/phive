<?php
namespace PharIo\Phive\Cli;

class OutputFactory {

    /**
     * @param bool $outputProgress
     *
     * @return Output
     */
    public function getConsoleOutput($outputProgress) {
        return new ConsoleOutput(ConsoleOutput::VERBOSE_INFO, $outputProgress);
    }

    /**
     * @param bool $outputProgress
     *
     * @return Output
     */
    public function getColoredConsoleOutput($outputProgress) {
        return new ColoredConsoleOutput(ConsoleOutput::VERBOSE_INFO, $outputProgress);
    }

}
