<?php declare(strict_types = 1);
namespace PharIo\Phive;

class MigrationService {
    /** @var MigrationFactory */
    private $factory;

    /**
     * MigrationService constructor.
     */
    public function __construct(MigrationFactory $factory) {
        $this->factory = $factory;
    }

    public function ensureFitness(): void {
        $this->runMigrations(true);
        $failed = [];

        foreach ($this->factory->getMigrations() as $migration) {
            if ($migration->mustMigrate() && $migration->inError()) {
                $failed[] = $migration->getDescription();
            }
        }

        if (\count($failed) > 0) {
            throw new MigrationsFailedException($failed);
        }
    }

    /**
     * @return Migration[]
     */
    public function getAllMigration(): array {
        return $this->factory->getMigrations();
    }
    public function runAll(): int {
        return $this->runMigrations(false);
    }
    private function runMigrations(bool $mustOnly): int {
        $migrationDone = 0;

        foreach ($this->factory->getMigrations() as $migration) {
            if ((!$mustOnly || $migration->mustMigrate()) && $migration->canMigrate()) {
                $migration->migrate();
                $migrationDone++;
            }
        }

        return $migrationDone;
    }
}
