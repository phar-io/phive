<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class HomePhiveXmlMigration extends InternalFileMigration {
    public function __construct(Config $config) {
        parent::__construct(
            $config->getHomeDirectory()->file('phive.xml'),
            $config->getGlobalInstallation()
        );
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function getDescription(): string {
        return 'Change the name of globally installed Phars configuration file.';
    }

    public function isUserMigration(): bool {
        return false;
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->putContent($legacy->read()->getContent());
    }

    protected function getFileDescription(): string {
        return 'global phive configuration';
    }
}
