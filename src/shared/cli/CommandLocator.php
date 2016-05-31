<?php
namespace PharIo\Phive\Cli;

interface CommandLocator {

    /**
     * @param string $command
     *
     * @throws CommandLocatorException
     * @return Command
     */
    public function getCommand($command);
}
