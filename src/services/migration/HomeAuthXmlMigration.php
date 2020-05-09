<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HomeAuthXmlMigration implements Migration {
    /** @var Cli\Output */
    private $output;
    /** @var Cli\Input */
    private $input;
    /** @var Config */
    private $config;

    public function __construct(Config $config, Cli\Output $output, Cli\Input $input) {
        $this->output      = $output;
        $this->input       = $input;
        $this->config      = $config;
    }

    public function canMigrate(): bool {
        return $this->config->getHomeDirectory()->file('phive-auth.xml')->exists()
            && !$this->config->getGlobalAuth()->exists();
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->config->getHomeDirectory()->file('phive-auth.xml');
        $new = $this->config->getGlobalAuth();

        $oldContent = $old->read()->getContent();
        $newContent = \str_replace('https://phar.io/phive-auth', 'https://phar.io/auth', $oldContent);
        $new->putContent($newContent);

        $this->output->writeText('Migration of global authentication configuration almost finish. ');

        if ($this->input->confirm('Do you want to keep the old file?', true)) {
            $old->renameTo('phive-auth.xml.backup');
        } else {
            $old->delete();
        }
    }

    public function getDescription(): string {
        return 'Change the name of global authentication configuration.';
    }

    public function inError(): bool {
        return $this->config->getHomeDirectory()->file('phive-auth.xml')->exists()
            && $this->config->getGlobalAuth()->exists();
    }
}
