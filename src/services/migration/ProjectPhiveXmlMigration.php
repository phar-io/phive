<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class ProjectPhiveXmlMigration extends UserFileMigration {
    public function __construct(Config $config, Cli\Input $input) {
        parent::__construct(
            $input,
            $config->getWorkingDirectory()->file('phive.xml'),
            $config->getProjectInstallation()
        );
    }

    public function mustMigrate(): bool {
        return false;
    }

    public function getDescription(): string {
        return 'Move the \'phive.xml\' inside the new \'.phive/\' configuration directory.';
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->putContent($legacy->read()->getContent());
    }

    protected function getFileDescription(): string {
        return 'project Phive configuration';
    }
}
