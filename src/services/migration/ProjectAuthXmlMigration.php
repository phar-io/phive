<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class ProjectAuthXmlMigration extends FileMigration {
    public function __construct(Environment $environment, Config $config, Cli\Input $input) {
        parent::__construct(
            $input,
            $environment->getWorkingDirectory()->file('phive-auth.xml'),
            $config->getProjectAuth()
        );
    }

    public function mustMigrate(): bool {
        return false;
    }

    public function getDescription(): string {
        return 'Move the \'phive-auth.xml\' inside the new \'.phive/\' configuration directory.';
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $oldContent = $legacy->read()->getContent();
        $newContent = \str_replace('https://phar.io/phive-auth', 'https://phar.io/auth', $oldContent);
        $new->putContent($newContent);
    }

    protected function getFileDescription(): string {
        return 'authentication configuration';
    }
}
