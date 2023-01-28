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

use function rtrim;

class SkelCommandConfig {
    /** @var Cli\Options */
    private $cliOptions;

    /** @var string */
    private $workingDirectory;

    public function __construct(Cli\Options $cliOptions, string $workingDirectory) {
        $this->cliOptions       = $cliOptions;
        $this->workingDirectory = rtrim($workingDirectory, '/');
    }

    public function allowOverwrite(): bool {
        return $this->cliOptions->hasOption('force');
    }

    public function getDestination(): string {
        if ($this->cliOptions->hasOption('auth')) {
            return $this->workingDirectory . '/.phive/auth.xml';
        }

        return $this->workingDirectory . '/.phive/phars.xml';
    }

    public function getTemplateFilename(): string {
        if ($this->cliOptions->hasOption('auth')) {
            return __DIR__ . '/../../../conf/auth.skeleton.xml';
        }

        return __DIR__ . '/../../../conf/phive.skeleton.xml';
    }
}
