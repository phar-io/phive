<?php declare(strict_types = 1);
namespace PharIo\Phive;

class VersionCommand implements Cli\Command {
    public function execute(): void {
        // nothing to be done here since the version string output happens in the runner
    }
}
