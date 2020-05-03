<?php declare(strict_types = 1);
namespace PharIo\Phive;

class SkelCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;

    /** @var string */
    private $workingDirectory;

    public function __construct(Cli\Options $cliOptions, string $workingDirectory) {
        $this->cliOptions       = $cliOptions;
        $this->workingDirectory = \rtrim($workingDirectory, '/');
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
