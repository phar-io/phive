<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Context;
use PharIo\Phive\Cli\Options;

class PhiveContext implements Context {

    /**
     * @var Options
     */
    private $options;

    /**
     * PhiveContext constructor.
     */
    public function __construct() {
        $this->options = new Options();
    }

    /**
     * @return Options
     */
    public function getOptions() {
        return $this->options;
    }

    public function knowsOption($option) {
        return false;
    }

    public function requiresValue($option) {
        return false;
    }

    public function getOptionForChar($char) {
    }

    public function acceptsArguments() {
        return $this->options->getArgumentCount() === 0;
    }

    public function canContinue() {
        return $this->options->getArgumentCount() === 0;
    }

    public function addArgument($arg) {
        $this->options->addArgument($arg);
    }

    public function setOption($option, $value) {
        $this->options->setOption($option, $value);
    }

}
