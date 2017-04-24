<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class LocalSourcesListFileLoader implements SourcesListFileLoader {

    /**
     * @var Filename
     */
    private $filename;

    /**
     * @param Filename $filename
     */
    public function __construct(Filename $filename) {
        $this->filename = $filename;
    }

    /**
     * @return SourcesList
     */
    public function load() {
        return new SourcesList(
            new XmlFile(
                $this->filename,
                'https://phar.io/repository-list',
                'repositories'
            )
        );
    }

}
