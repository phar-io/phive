<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HomePharsXmlMigration implements Migration {
    /** @var Config */
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function canMigrate(): bool {
        return $this->config->getHomeDirectory()->file('phars.xml')->exists()
            && !$this->config->getHomeDirectory()->file('database.xml')->exists();
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->config->getHomeDirectory()->file('phars.xml');
        $new = $this->config->getHomeDirectory()->file('database.xml');

        $new->putContent($old->read()->getContent());
        $old->delete();
    }

    public function getDescription(): string {
        return 'Change the name of the list of all installed Phars file.';
    }

    public function inError(): bool {
        return $this->config->getHomeDirectory()->file('phars.xml')->exists()
            && $this->config->getHomeDirectory()->file('database.xml')->exists();
    }
}
