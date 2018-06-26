<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class GlobalPhiveXmlConfig extends PhiveXmlConfig {

    /**
     * @param InstalledPhar $installedPhar
     *
     * @return Filename
     */
    protected function getLocation(InstalledPhar $installedPhar) {
        return $installedPhar->getLocation()->withAbsolutePath();
    }

}
