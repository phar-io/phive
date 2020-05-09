<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ProjectPhiveXmlMigration implements Migration {
    /** @var Environment */
    private $environment;
    /** @var Cli\Output */
    private $output;
    /** @var Cli\Input */
    private $input;
    /** @var Config */
    private $config;

    public function __construct(Environment $environment, Config $config, Cli\Output $output, Cli\Input $input) {
        $this->environment = $environment;
        $this->output      = $output;
        $this->input       = $input;
        $this->config      = $config;
    }

    public function canMigrate(): bool {
        return $this->environment->getWorkingDirectory()->file('phive.xml')->exists()
            && (
                !$this->environment->getWorkingDirectory()->hasChild('.phive')
                || !$this->config->getProjectInstallation()->exists()
            );
    }

    public function mustMigrate(): bool {
        return false;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->environment->getWorkingDirectory()->file('phive.xml');
        $new = $this->config->getProjectInstallation();

        $new->putContent($old->read()->getContent());

        $this->output->writeText('Migration of project Phive configuration almost finish. ');

        if ($this->input->confirm('Do you want to keep the old file?', true)) {
            $old->renameTo('phive.xml.backup');
        } else {
            $old->delete();
        }
    }

    public function getDescription(): string {
        return 'Move the \'phive.xml\' inside the new \'.phive/\' configuration directory.';
    }

    public function inError(): bool {
        return $this->environment->getWorkingDirectory()->file('phive.xml')->exists()
            && $this->environment->getWorkingDirectory()->hasChild('.phive')
            && $this->config->getProjectInstallation()->exists();
    }
}
