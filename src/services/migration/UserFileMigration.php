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

use function basename;
use function sprintf;
use PharIo\FileSystem\Filename;

abstract class UserFileMigration extends FileMigration {
    /** @var Cli\Input */
    private $input;

    public function __construct(Cli\Input $input, Filename $legacy, Filename $new) {
        $this->input = $input;
        parent::__construct($legacy, $new);
    }

    public function isUserMigration(): bool {
        return true;
    }

    abstract protected function getFileDescription(): string;

    protected function handleOldFile(Filename $old): void {
        $message = sprintf('Migration of %s is almost finished. Do you want to keep the old file?', $this->getFileDescription());

        if ($this->input->confirm($message, true)) {
            $newName = basename($old->asString()) . '.backup';
            $old->renameTo($newName);
        } else {
            $old->delete();
        }
    }
}
