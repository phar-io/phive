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

interface Migration {
    /**
     * Indicate if the migration can be done.
     * Return false if the migration is already done, or no doable.
     */
    public function canMigrate(): bool;

    /**
     * Indicate if we allow the state before and after at the same time (false).
     * Return true if only the new state is allowed.
     */
    public function mustMigrate(): bool;

    /**
     * Indicate if the migration is a user/project related migration or
     * an Phive/internal migration.
     */
    public function isUserMigration(): bool;
    public function inError(): bool;
    public function getDescription(): string;
    public function migrate(): void;
}
