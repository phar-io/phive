<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class SymlinkPharActivator implements PharActivator {

    /**
     * @param Filename $pharLocation
     * @param Filename $linkDestination
     *
     * @return Filename
     */
    public function activate(Filename $pharLocation, Filename $linkDestination) {
        symlink($pharLocation->asString(), $linkDestination->asString());
        return new Filename($linkDestination->asString());
    }

}
