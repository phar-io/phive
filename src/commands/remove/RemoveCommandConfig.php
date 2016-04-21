<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class RemoveCommandConfig {

    /**
     * @var Cli\Options
     */
    private $cliOptions;

    /**
     * @var TargetDirectoryLocator
     */
    private $targetDirectoryLocator;

    /**
     * @param Cli\Options            $options
     * @param TargetDirectoryLocator $targetDirectoryLocator
     */
    public function __construct(Cli\Options $options, TargetDirectoryLocator $targetDirectoryLocator) {
        $this->cliOptions = $options;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    /**
     * @return Directory
     */
    public function getTargetDirectory() {
        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @return string
     * @throws Cli\CommandOptionsException
     */
    public function getPharName() {
        return $this->cliOptions->getArgument(0);
    }

}
