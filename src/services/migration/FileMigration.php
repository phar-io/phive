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

abstract class FileMigration implements Migration {
    /** @var Filename */
    private $legacy;
    /** @var Filename */
    private $new;

    public function __construct(Filename $legacy, Filename $new) {
        $this->legacy = $legacy;
        $this->new    = $new;
    }

    public function canMigrate(): bool {
        return $this->legacy->exists() && !$this->new->exists();
    }

    public function inError(): bool {
        return $this->legacy->exists() && $this->new->exists();
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $this->doMigrate($this->legacy, $this->new);

        $this->handleOldFile($this->legacy);
    }

    abstract protected function doMigrate(Filename $legacy, Filename $new): void;

    abstract protected function handleOldFile(Filename $old): void;
}
