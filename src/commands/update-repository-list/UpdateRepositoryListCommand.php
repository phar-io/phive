<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\Command;

class UpdateRepositoryListCommand implements Command {

    /** @var RemoteSourcesListFileLoader */
    private $loader;

    public function __construct(RemoteSourcesListFileLoader $loader) {
        $this->loader = $loader;
    }

    public function execute(): void {
        $this->loader->downloadFromSource();
    }
}
