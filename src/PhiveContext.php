<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class PhiveContext extends GeneralContext {

    protected function getKnownOptions() {
        return [
            'home' => false,
            'no-progress' => false
        ];
    }

    /**
     * @param string $option
     *
     * @return bool
     */
    public function requiresValue($option) {
        return $option === 'home';
    }

    /**
     * @return bool
     */
    public function acceptsArguments() {
        return $this->getOptions()->getArgumentCount() === 0;
    }

    /**
     * @return bool
     */
    public function canContinue() {
        return $this->acceptsArguments();
    }

}
