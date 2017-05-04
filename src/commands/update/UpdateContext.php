<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class UpdateContext extends GeneralContext {

    /**
     * @return array
     */
    protected function getKnownOptions() {
        return [
            'prefer-offline' => false,
            'global'         => 'g'
        ];
    }

}
