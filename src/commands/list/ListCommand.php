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

use function count;
use function sort;

class ListCommand implements Cli\Command {
    /** @var SourcesList */
    private $sourcesList;

    /** @var SourcesList */
    private $localSources;

    /** @var Cli\Output */
    private $output;

    public function __construct(SourcesList $sourcesList, SourcesList $localSources, Cli\Output $output) {
        $this->sourcesList  = $sourcesList;
        $this->localSources = $localSources;
        $this->output       = $output;
    }

    public function execute(): void {
        $localAliases = $this->localSources->getAliases();

        if (count($localAliases) > 0) {
            $this->output->writeText("\nList of local aliases known to your system:\n");
            $this->printAliases($localAliases);
        }

        $this->output->writeText("\nList of phar.io resolved aliases known to your system:\n");
        $this->printAliases($this->sourcesList->getAliases());
    }

    private function printAliases(array $aliases): void {
        sort($aliases);

        foreach ($aliases as $aliasName) {
            $this->output->writeText("* {$aliasName}\n");
        }
    }
}
