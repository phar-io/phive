<?php

namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class UnixoidPharInstaller extends PharInstaller {

    protected function link(Filename $phar, Filename $destination) {
        $this->getOutput()->writeInfo(
            sprintf('Linking %s to %s', $phar->asString(), $destination->asString())
        );
        symlink($phar->asString(), $destination->asString());
    }

}
