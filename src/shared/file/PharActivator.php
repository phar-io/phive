<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

interface PharActivator {

    /**
     * @param Filename $pharLocation
     * @param Filename $linkDestination
     *
     * @return Filename
     */
    public function activate(Filename $pharLocation, Filename $linkDestination);
}
