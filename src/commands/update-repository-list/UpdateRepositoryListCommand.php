<?php
namespace PharIo\Phive;

class UpdateRepositoryListCommand {

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
