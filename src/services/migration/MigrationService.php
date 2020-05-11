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
    public function getUserMigrations(): array {
        return \array_filter($this->factory->getMigrations(), function (Migration $migration) {
            return !$migration->mustMigrate();
        });
    }
    public function runAll(): int {
        return $this->runMigrations(false);
    }
    public function runMandatory(): int {
        return $this->runMigrations(true);
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
