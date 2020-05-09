<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ProjectAuthXmlMigration implements Migration {
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    public function canMigrate(): bool {
        return $this->environment->getWorkingDirectory()->file('phive-auth.xml')->exists()
            && (
                !$this->environment->getWorkingDirectory()->hasChild('.phive')
                || !$this->environment->getWorkingDirectory()->child('.phive')->file('auth.xml')->exists()
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
        $new = $this->environment->getWorkingDirectory()->child('.phive')->file('auth.xml');

        $oldContent = $old->read()->getContent();
        $newContent = \str_replace('https://phar.io/phive-auth', 'https://phar.io/auth', $oldContent);
        $new->putContent($newContent);
        $old->delete();
    }

    public function getDescription(): string {
        return 'Move the \'phive-auth.xml\' inside the new \'.phive/\' configuration directory.';
    }

    public function inError(): bool {
        return $this->environment->getWorkingDirectory()->file('phive-auth.xml')->exists()
            && $this->environment->getWorkingDirectory()->hasChild('.phive')
            && $this->environment->getWorkingDirectory()->child('.phive')->file('auth.xml')->exists();
    }
}
