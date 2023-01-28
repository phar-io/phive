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

use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;
use const PHP_RELEASE_VERSION;
use const PHP_VERSION;
use function count;
use function extension_loaded;
use function implode;
use function sprintf;
use PharIo\Manifest\PhpExtensionRequirement;
use PharIo\Manifest\PhpVersionRequirement;
use PharIo\Phive\Cli\Input;
use PharIo\Phive\Cli\Output;
use PharIo\Version\InvalidVersionException;
use PharIo\Version\Version;

class CompatibilityService {
    /** @var Output */
    private $output;

    /** @var Input */
    private $input;

    /**
     * CompatibilityService constructor.
     */
    public function __construct(Output $output, Input $input) {
        $this->output = $output;
        $this->input  = $input;
    }

    public function canRun(Phar $phar): bool {
        if (!$phar->hasManifest()) {
            return true;
        }
        $issues = [];

        $manifest = $phar->getManifest();

        foreach ($manifest->getRequirements() as $requirement) {
            switch (true) {
                case $requirement instanceof PhpVersionRequirement: {
                    $php = $requirement->getVersionConstraint();

                    try {
                        $phpversion = new Version(PHP_VERSION);
                    } catch (InvalidVersionException $ex) {
                        $phpversion = new Version(PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION);
                    }

                    if (!$php->complies($phpversion)) {
                        $issues[] = sprintf(
                            'PHP Version %s required, but %s in use',
                            $php->asString(),
                            PHP_VERSION
                        );
                    }

                    continue 2;
                }

                case $requirement instanceof PhpExtensionRequirement: {
                    if (!extension_loaded($requirement->asString())) {
                        $issues[] = sprintf(
                            'Extension %s is required, but not installed or activated',
                            $requirement->asString()
                        );
                    }
                }
            }
        }

        if (!count($issues)) {
            return true;
        }

        return $this->confirmInstallation($issues);
    }

    private function confirmInstallation(array $issues): bool {
        $warning = sprintf(
            "Your environment does not seem to satisfy the needs this phar has:\n\n           %s\n",
            implode("\n           ", $issues)
        );

        $this->output->writeWarning($warning);

        return $this->input->confirm('Install anyway?', false);
    }
}
