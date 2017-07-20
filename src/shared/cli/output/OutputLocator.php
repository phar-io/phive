<?php
namespace PharIo\Phive\Cli;

use PharIo\Phive\Environment;

class OutputLocator {

    /**
     * @var OutputFactory
     */
    private $factory;

    /**
     * @param OutputFactory $factory
     */
    public function __construct(OutputFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param Environment $environment
     * @param bool $printProgressUpdates
     *
     * @return Output
     */
    public function getOutput(Environment $environment, $printProgressUpdates) {
        if ($environment->supportsColoredOutput()) {
            return $this->factory->getColoredConsoleOutput($printProgressUpdates);
        }

        return $this->factory->getConsoleOutput($printProgressUpdates);
    }

}
