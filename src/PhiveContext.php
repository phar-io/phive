<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class PhiveContext extends GeneralContext {

    protected function getKnownOptions() {
        return ['home'];
    }

    public function requiresValue($option) {
        return $option === 'home';
    }

    public function acceptsArguments() {
        return $this->getOptions()->getArgumentCount() === 0;
    }

    public function canContinue() {
        return $this->acceptsArguments();
    }

}
