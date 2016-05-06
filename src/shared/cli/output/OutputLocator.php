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
     *
     * @return Output
     */
    public function getOutput(Environment $environment) {
        if ($environment->supportsColoredOutput()) {
            return $this->factory->getColoredConsoleOutput();
        }
        return $this->factory->getConsoleOutput();
    }

}