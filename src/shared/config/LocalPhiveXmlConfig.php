<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class LocalPhiveXmlConfig extends PhiveXmlConfig {

    /**
     * @param InstalledPhar $installedPhar
     *
     * @return Filename
     */
    protected function getLocation(InstalledPhar $installedPhar) {
        return $installedPhar->getLocation()->getRelativePathTo($this->getOwnDirectory());
    }

}
