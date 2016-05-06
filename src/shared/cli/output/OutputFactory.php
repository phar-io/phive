<?php
namespace PharIo\Phive\Cli;

class OutputFactory {

    /**
     * @return Output
     */
    public function getConsoleOutput() {
        return new ConsoleOutput(ConsoleOutput::VERBOSE_INFO);
    }

    /**
     * @return Output
     */
    public function getColoredConsoleOutput() {
        return new ColoredConsoleOutput(ConsoleOutput::VERBOSE_INFO);
    }
    
}