<?php
namespace PharIo\Phive;

interface SourcesListFileLoader {

    /**
     * @return SourcesList
     */
    public function load();

}