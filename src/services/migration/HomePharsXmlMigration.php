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
        return 'Rename internal storage file from `phars.xml` to `registry.xml`.';
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->putContent($legacy->read()->getContent());
    }
}
