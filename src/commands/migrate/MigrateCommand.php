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

use function sprintf;
use PharIo\Phive\Cli\ConsoleTable;

class MigrateCommand implements Cli\Command {
    /** @var MigrateCommandConfig */
    private $config;

    /** @var Cli\Output */
    private $output;

    /** @var MigrationService */
    private $migrationService;

    /**
     * @internal param PharService $pharService
     */
    public function __construct(
        MigrationService $migrationService,
        MigrateCommandConfig $config,
        Cli\Output $output
    ) {
        $this->output           = $output;
        $this->migrationService = $migrationService;
        $this->config           = $config;
    }

    public function execute(): void {
        if ($this->config->showStatus()) {
            $migrations = $this->migrationService->getUserMigrations();
            $table      = new ConsoleTable(['Status', 'Description']);

            foreach ($migrations as $migration) {
                if ($migration->canMigrate()) {
                    $status = 'Not applied';
                } elseif ($migration->inError()) {
                    $status = 'Can\'t migrate';
                } else {
                    continue;
                }

                $table->addRow([
                    $status,
                    $migration->getDescription()
                ]);
            }
            $this->output->writeText($table->asString());

            return;
        }
        $executed = $this->migrationService->runAll();
        $this->output->writeInfo(sprintf('%d migration%s have been done.', $executed, $executed !== 1 ? 's' : ''));
    }
}
