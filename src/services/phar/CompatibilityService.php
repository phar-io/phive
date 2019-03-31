<?php declare(strict_types = 1);
namespace PharIo\Phive;

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
                        $phpversion = new Version(\PHP_VERSION);
                    } catch (InvalidVersionException $ex) {
                        $phpversion = new Version(\PHP_MAJOR_VERSION . '.' . \PHP_MINOR_VERSION . '.' . \PHP_RELEASE_VERSION);
                    }

                    if (!$php->complies($phpversion)) {
                        $issues[] = \sprintf(
                            'PHP Version %s required, but %s in use',
                            $php->asString(),
                            \PHP_VERSION
                        );
                    }

                    continue 2;
                }

                case $requirement instanceof PhpExtensionRequirement: {
                    if (!\extension_loaded((string)$requirement)) {
                        $issues[] = \sprintf(
                            'Extension %s is required, but not installed or activated',
                            $requirement
                        );
                    }
                }
            }
        }

        if (!\count($issues)) {
            return true;
        }

        return $this->confirmInstallation($issues);
    }

    private function confirmInstallation(array $issues): bool {
        $warning = \sprintf(
            "Your environment does not seem to satisfy the needs this phar has:\n\n           %s\n",
            \implode("\n           ", $issues)
        );

        $this->output->writeWarning($warning);

        return $this->input->confirm('Install anyway?', false);
    }
}
