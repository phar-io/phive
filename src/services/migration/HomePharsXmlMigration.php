<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HomePharsXmlMigration implements Migration {
    /** @var Config */
    private $config;
    /** @var Cli\Output */
    private $output;
    /** @var Cli\Input */
    private $input;

    public function __construct(Config $config, Cli\Output $output, Cli\Input $input) {
        $this->output = $output;
        $this->input  = $input;
        $this->config = $config;
    }

    public function canMigrate(): bool {
        return $this->config->getHomeDirectory()->file('phars.xml')->exists()
            && !$this->config->getRegistry()->exists();
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->config->getHomeDirectory()->file('phars.xml');
        $new = $this->config->getRegistry();

        $new->putContent($old->read()->getContent());

        $this->output->writeText('Migration of global authentication configuration almost finish.');

        if ($this->input->confirm('Do you want to keep the old file?', true)) {
            $old->renameTo('phars.xml.backup');
        } else {
            $old->delete();
        }
    }

    public function getDescription(): string {
        return 'Change the name of the list of all installed Phars file.';
    }

    public function inError(): bool {
        return $this->config->getHomeDirectory()->file('phars.xml')->exists()
            && $this->config->getRegistry()->exists();
    }
}
