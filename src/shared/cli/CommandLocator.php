<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

interface CommandLocator {

    /**
     * @throws CommandLocatorException
     */
    public function getCommand(string $command): Command;
}
