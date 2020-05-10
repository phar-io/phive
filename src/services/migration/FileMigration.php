<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

abstract class FileMigration implements Migration {
    /** @var Cli\Input */
    private $input;
    /** @var Filename */
    private $legacy;
    /** @var Filename */
    private $new;

    public function __construct(Cli\Input $input, Filename $legacy, Filename $new) {
        $this->input       = $input;
        $this->legacy      = $legacy;
        $this->new         = $new;
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

        $this->handleOldFile($this->getFileDescription(), $this->legacy);
    }

    abstract protected function doMigrate(Filename $legacy, Filename $new): void;

    abstract protected function getFileDescription(): string;

    protected function handleOldFile(string $description, Filename $old): void {
        $message = \sprintf('Migration of %s is almost finished. Do you want to keep the old file?', $description);

        if ($this->input->confirm($message, true)) {
            $newName = \basename($old->asString()) . '.backup';
            $old->renameTo($newName);
        } else {
            $old->delete();
        }
    }
}
