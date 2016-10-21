<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Command;

class UpdateRepositoryListCommand implements Command {

    /**
     * @var RemoteSourcesListFileLoader
     */
    private $loader;

    /**
     * @param RemoteSourcesListFileLoader $loader
     */
    public function __construct(RemoteSourcesListFileLoader $loader) {
        $this->loader = $loader;
    }

    public function execute() {
        $this->loader->downloadFromSource();
    }

}
