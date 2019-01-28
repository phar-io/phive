<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

use PharIo\Phive\Environment;

class OutputLocator {

    /** @var OutputFactory */
    private $factory;

    public function __construct(OutputFactory $factory) {
        $this->factory = $factory;
    }

    public function getOutput(Environment $environment, bool $printProgressUpdates): Output {
        if ($environment->supportsColoredOutput()) {
            return $this->factory->getColoredConsoleOutput($printProgressUpdates);
        }

        return $this->factory->getConsoleOutput($printProgressUpdates);
    }
}
