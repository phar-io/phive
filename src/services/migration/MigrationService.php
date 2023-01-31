<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function array_filter;
use function count;

class MigrationService {
    /** @var MigrationFactory */
    private $factory;

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

        if (count($failed) > 0) {
            throw new MigrationsFailedException($failed);
        }
    }

    /**
     * @return Migration[]
     */
    public function getUserMigrations(): array {
        return array_filter($this->factory->getMigrations(), static function (Migration $migration) {
            return $migration->isUserMigration();
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
