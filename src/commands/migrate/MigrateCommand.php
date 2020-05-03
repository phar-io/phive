<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\ConsoleTable;

class MigrateCommand implements Cli\Command {

    /** @var MigrateCommandConfig */
    private $config;

    /** @var PharRegistry */
    private $pharRegistry;

    /** @var Cli\Output */
    private $output;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;
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
        if ($this->config->showList()) {
            $migrations = $this->migrationService->getAllMigration();
            $table      = new ConsoleTable(['Status', 'Mandatory', 'Description']);

            foreach ($migrations as $migration) {
                switch (true) {
                    case $migration->canMigrate():
                        $status = 'Not applied';

                        break;
                    case $migration->inError():
                        $status = 'Can\'t migrate';

                        break;
                    default:
                        $status = 'Migrated';
                }

                $table->addRow([
                    $status,
                    $migration->mustMigrate() ? 'Yes' : 'No',
                    $migration->getDescription()
                ]);
            }
            $this->output->writeText($table->asString());

            return;
        }
        $executed = $this->migrationService->runAll();
        $this->output->writeInfo(\sprintf('%d migration%s have been done.', $executed, $executed !== 1 ? 's':''));
    }
}
