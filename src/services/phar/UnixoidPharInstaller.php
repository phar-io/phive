<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function sprintf;
use function symlink;
use PharIo\FileSystem\Filename;

class UnixoidPharInstaller extends PharInstaller {
    protected function link(Filename $phar, Filename $destination): void {
        $this->getOutput()->writeInfo(
            sprintf('Linking %s to %s', $phar->asString(), $destination->asString())
        );

        symlink(
            $phar->withAbsolutePath()->asString(),
            $destination->withAbsolutePath()->asString()
        );
    }
}
