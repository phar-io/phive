<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class LocalPhiveXmlConfig extends PhiveXmlConfig {
    protected function getLocation(InstalledPhar $installedPhar): Filename {
        return $installedPhar->getLocation()->getRelativePathTo($this->getOwnDirectory());
    }
}
