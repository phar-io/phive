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

use PharIo\FileSystem\Filename;

class HomePharsXmlMigration implements Migration {
    /** @var Filename */
    private $legacy;

    /** @var Filename */
    private $registry;

    public function __construct(Config $config) {
        $this->legacy   = $config->getHomeDirectory()->file('phars.xml');
        $this->registry = $config->getRegistry();
    }

    public function canMigrate(): bool {
        return $this->mustMigrate() && !$this->inError();
    }

    public function mustMigrate(): bool {
        if (!$this->legacy->exists()) {
            return false;
        }

        // if this is really our installdb file, we want to migrate it - otherwise we ignore it
        return strpos($this->legacy->read()->getContent(), 'xmlns="https://phar.io/phive/installdb"') !== false;
    }

    public function isUserMigration(): bool {
        return false;
    }

    public function inError(): bool {
        return $this->legacy->exists() && $this->registry->exists();
    }

    public function getDescription(): string {
        return 'Rename internal storage file from `phars.xml` to `registry.xml`.';
    }

    public function migrate(): void {
        $this->registry->putContent(
            $this->legacy->read()->getContent()
        );

        $this->legacy->delete();
    }
}
