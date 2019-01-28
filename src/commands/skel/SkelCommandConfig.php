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
        return $this->workingDirectory . '/phive.xml';
    }

    public function getTemplateFilename(): string {
        return __DIR__ . '/../../../conf/phive.skeleton.xml';
    }
}
