<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ProjectAuthXmlMigration implements Migration {
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
        return $this->environment->getWorkingDirectory()->file('phive-auth.xml')->exists()
            && (
                !$this->environment->getWorkingDirectory()->hasChild('.phive')
                || !$this->config->getProjectAuth()->exists()
            );
    }

    public function mustMigrate(): bool {
        return false;
    }

    public function migrate(): void {
        if (!$this->canMigrate()) {
            throw new MigrationException();
        }

        $old = $this->environment->getWorkingDirectory()->file('phive-auth.xml');
        $new = $this->config->getProjectAuth();

        $oldContent = $old->read()->getContent();
        $newContent = \str_replace('https://phar.io/phive-auth', 'https://phar.io/auth', $oldContent);
        $new->putContent($newContent);

        $this->output->writeText('Migration of authentication configuration almost finish. ');

        if ($this->input->confirm('Do you want to keep the old file?', true)) {
            $old->renameTo('phive-auth.xml.backup');
        } else {
            $old->delete();
        }
    }

    public function getDescription(): string {
        return 'Move the \'phive-auth.xml\' inside the new \'.phive/\' configuration directory.';
    }

    public function inError(): bool {
        return $this->environment->getWorkingDirectory()->file('phive-auth.xml')->exists()
            && $this->environment->getWorkingDirectory()->hasChild('.phive')
            && $this->config->getProjectAuth()->exists();
    }
}
