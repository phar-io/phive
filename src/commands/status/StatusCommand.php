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

use function array_map;
use function array_values;
use function count;
use function implode;
use function substr;
use PharIo\Phive\Cli\ConsoleTable;

class StatusCommand implements Cli\Command {
    /** @var Cli\Output */
    private $output;

    /** @var PharRegistry */
    private $pharRegistry;

    /** @var StatusCommandConfig */
    private $statusCommandConfig;

    public function __construct(
        StatusCommandConfig $statusCommandConfig,
        PharRegistry $pharRegistry,
        Cli\Output $output
    ) {
        $this->pharRegistry        = $pharRegistry;
        $this->output              = $output;
        $this->statusCommandConfig = $statusCommandConfig;
    }

    public function execute(): void {
        $allInstalled    = $this->statusCommandConfig->allInstalled();
        $globalInstalled = $this->statusCommandConfig->globalInstalled();

        if ($allInstalled || $globalInstalled) {
            $this->showForSystem();

            return;
        }

        $this->showForProject();
    }

    private function showForProject(): void {
        $phars = $this->statusCommandConfig->getPhars();

        if (count($phars) === 0) {
            $this->output->writeText("\nNo PHARs configured for this project.\n\n");

            return;
        }

        $this->output->writeText('PHARs configured in project:' . "\n\n");

        $table = new ConsoleTable(['Alias/URL', 'Version Constraint', 'Installed', 'Location', 'Key Ids']);

        foreach ($phars as $phar) {
            $table->addRow($this->buildRow($phar));
        }

        $this->output->writeText($table->asString());
    }

    private function showForSystem(): void {
        $phars = $this->statusCommandConfig->getPhars();

        if (count($phars) === 0) {
            $this->output->writeText("\nNo PHARs configured in your system.\n");

            return;
        }

        $this->output->writeText('PHARs configured in your system:' . "\n\n");

        $table = new ConsoleTable(['Alias/URL', 'Version', 'Usage Location', 'Key Ids']);

        foreach ($phars as $phar) {
            $row = $this->buildRow($phar);
            unset($row[1]);
            $table->addRow(array_values($row));
        }

        $this->output->writeText($table->asString());
    }

    private function buildRow(ConfiguredPhar $phar): array {
        $installed = '-';

        if ($phar->isInstalled()) {
            $installed = $phar->getInstalledVersion()->getVersionString();
        }
        $location = $phar->hasLocation() ? $phar->getLocation()->asString() : '-';
        $keys     = implode(
            ', ',
            array_map(
                static function ($key) {
                    return substr($key, -16);
                },
                $this->pharRegistry->getKnownSignatureFingerprints($phar->getName())
            )
        );

        return [$phar->getName(), $phar->getVersionConstraint()->asString(), $installed, $location, $keys];
    }
}
