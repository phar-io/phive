<?php
namespace PharIo\Phive\Cli;

interface CommandLocator {

    /**
     * @param Request $request
     *
     * @throws CommandLocatorException
     * @return Command
     */
    public function getCommandForRequest(Request $request);
}
