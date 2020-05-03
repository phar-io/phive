<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ProjectPhiveXmlMigration implements Migration {
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    public function canMigrate(): bool {
        return $this->environment->getWorkingDirectory()->file('phive.xml')->exists()
            && (
                !$this->environment->getWorkingDirectory()->hasChild('.phive')
                || !$this->environment->getWorkingDirectory()->child('.phive')->file('phars.xml')->exists()
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
        $new = $this->environment->getWorkingDirectory()->child('.phive')->file('phars.xml');

        $new->putContent($old->read()->getContent());
        $old->delete();
    }

    public function getDescription(): string {
        return 'Move the \'phive.xml\' inside the new \'.phive/\' configuration directory.';
    }

    public function inError(): bool {
        return $this->environment->getWorkingDirectory()->file('phive.xml')->exists()
            && $this->environment->getWorkingDirectory()->hasChild('.phive')
            && $this->environment->getWorkingDirectory()->child('.phive')->file('phars.xml')->exists();
    }
}
