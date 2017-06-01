<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

class MacOsEnvironment extends UnixoidEnvironment {

    /**
     * @return Directory
     */
    public function getGlobalBinDir() {
        return new Directory('/usr/local/bin');
    }

}
