<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HomePhiveXmlMigration implements Migration {
    /** @var Config */
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function canMigrate(): bool {
        return $this->config->getHomeDirectory()->file('phive.xml')->exists()
            && !$this->config->getHomeDirectory()->file('global.xml')->exists();
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->config->getHomeDirectory()->file('phive.xml');
        $new = $this->config->getHomeDirectory()->file('global.xml');

        $new->putContent($old->read()->getContent());
        $old->delete();
    }

    public function getDescription(): string {
        return 'Change the name of globally installed Phars configuration file.';
    }

    public function inError(): bool {
        return $this->config->getHomeDirectory()->file('phive.xml')->exists()
            && $this->config->getHomeDirectory()->file('global.xml')->exists();
    }
}
