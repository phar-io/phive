<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class UnixoidPharInstaller extends PharInstaller {
    protected function link(Filename $phar, Filename $destination): void {
        $this->getOutput()->writeInfo(
            \sprintf('Linking %s to %s', $phar->asString(), $destination->asString())
        );

        \symlink(
            $phar->withAbsolutePath()->asString(),
            $destination->withAbsolutePath()->asString()
        );
    }
}
