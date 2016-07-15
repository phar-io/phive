<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Command;

class UpdateRepositoryListCommand implements Command {

    /**
     * @var SourcesListFileLoader
     */
    private $loader;

    /**
     * @param SourcesListFileLoader $loader
     */
    public function __construct(SourcesListFileLoader $loader) {
        $this->loader = $loader;
    }

    /**
     *
     */
    public function execute() {
        $this->loader->downloadFromSource();
    }

}
