<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class RemoveContext extends GeneralContext {
    /**
     * @return array
     */
    protected function getKnownOptions() {
        return [
            'global'         => 'g'
        ];
    }
}
