<?php declare(strict_types = 1);
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
        return 'Change the name of the list of all installed Phars file.';
    }

    public function isUserMigration(): bool {
        return false;
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->putContent($legacy->read()->getContent());
    }

    protected function getFileDescription(): string {
        return 'list of installed phars';
    }
}
