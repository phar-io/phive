<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class ProjectPhiveXmlMigration extends FileMigration {
    public function __construct(Environment $environment, Config $config, Cli\Input $input) {
        parent::__construct(
            $input,
            $environment->getWorkingDirectory()->file('phive.xml'),
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
