<?php declare(strict_types = 1);
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

use PharIo\FileSystem\Filename;

class HomePharsXmlMigration extends InternalFileMigration {
    public function __construct(Config $config) {
        parent::__construct(
            $config->getHomeDirectory()->file('phars.xml'),
            $config->getRegistry()
        );
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function getDescription(): string {
        return 'Rename internal storage file from `phars.xml` to `registry.xml`.';
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->putContent($legacy->read()->getContent());
    }
}
