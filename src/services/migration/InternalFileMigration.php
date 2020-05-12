<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

abstract class InternalFileMigration extends FileMigration {
    public function isUserMigration(): bool {
        return false;
    }

    protected function handleOldFile(Filename $old): void {
        $old->delete();
    }
}
