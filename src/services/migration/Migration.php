<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface Migration {
    /**
     * Indicate if the migration can be done.
     * Return false if the migration is already done, or no doable
     */
    public function canMigrate(): bool;

    /**
     * Indicate if we allow the state before and after at the same time (false).
     * Return true if only the new state is allowed
     */
    public function mustMigrate(): bool;
    public function inError(): bool;
    public function getDescription(): string;
    public function migrate(): void;
}
