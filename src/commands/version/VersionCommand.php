<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class VersionCommand implements Cli\Command {

    public function execute() {
        // nothing to be done here since the version string output happens in the runner
    }

}
