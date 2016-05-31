<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Context;
use PharIo\Phive\Cli\Options;

class InstallContext implements Context {

    /**
     * @var Options
     */
    private $options;

    /**
     * InstallContext constructor.
     */
    public function __construct() {
        $this->options = new Options();
    }

    public function canContinue() {
        return true;
    }

    public function knowsOption($option) {
        return false;
    }

    public function requiresValue($option) {
        // TODO: Implement requiresValue() method.
        return false;
    }

    public function getOptionForChar($char) {
        return null;
    }

    public function acceptsArguments() {
        return true;
    }

    public function addArgument($arg) {
        $this->options->addArgument($arg);
    }

    public function setOption($option, $value) {
    }

    public function getOptions() {
        return $this->options;
    }

}
