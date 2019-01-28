<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

class RemoveCommandConfig {

    /** @var Cli\Options */
    private $cliOptions;

    /** @var TargetDirectoryLocator */
    private $targetDirectoryLocator;

    public function __construct(Cli\Options $options, TargetDirectoryLocator $targetDirectoryLocator) {
        $this->cliOptions             = $options;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    public function getTargetDirectory(): Directory {
        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @throws Cli\CommandOptionsException
     */
    public function getPharName(): string {
        return $this->cliOptions->getArgument(0);
    }
}
