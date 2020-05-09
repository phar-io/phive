<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HomePhiveXmlMigration implements Migration {
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
        return $this->config->getHomeDirectory()->file('phive.xml')->exists()
            && !$this->config->getGlobalInstallation()->exists();
    }

    public function mustMigrate(): bool {
        return true;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->config->getHomeDirectory()->file('phive.xml');
        $new = $this->config->getGlobalInstallation();

        $new->putContent($old->read()->getContent());

        $this->output->writeText('Migration of global phive configuration almost finish.');

        if ($this->input->confirm('Do you want to keep the old file?', true)) {
            $old->renameTo('phive.xml.backup');
        } else {
            $old->delete();
        }
    }

    public function getDescription(): string {
        return 'Change the name of globally installed Phars configuration file.';
    }

    public function inError(): bool {
        return $this->config->getHomeDirectory()->file('phive.xml')->exists()
            && $this->config->getGlobalInstallation()->exists();
    }
}
