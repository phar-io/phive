<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class HomePhiveXmlMigration extends FileMigration {
    public function __construct(Config $config, Cli\Input $input) {
        parent::__construct(
            $input,
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

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->putContent($legacy->read()->getContent());
    }

    protected function getFileDescription(): string {
        return 'global phive configuration';
    }
}
