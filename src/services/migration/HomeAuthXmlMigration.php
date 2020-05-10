<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class HomeAuthXmlMigration extends FileMigration {
    public function __construct(Config $config, Cli\Input $input) {
        parent::__construct(
            $input,
            $config->getHomeDirectory()->file('phive-auth.xml'),
            $config->getGlobalAuth()
        );
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function getDescription(): string {
        return 'Change the name of global authentication configuration.';
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $oldContent = $legacy->read()->getContent();
        $newContent = \str_replace('https://phar.io/phive-auth', 'https://phar.io/auth', $oldContent);
        $new->putContent($newContent);
    }

    protected function getFileDescription(): string {
        return 'global authentication configuration';
    }
}
